<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\MaterialTransaction;
use App\Models\Setting;
use App\Mail\MaterialReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDailyMaterialReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materials:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily material reminders to employees who haven\'t logged materials today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if materials feature is enabled system-wide
        $materialsEnabled = Setting::where('key', 'materials_enabled')->value('value') !== '0';
        if (!$materialsEnabled) {
            $this->info('Materials feature is system-wide disabled. Aborting.');
            return 0;
        }

        // First check if today is a weekday (Monday to Friday)
        if (Carbon::now()->isWeekend()) {
            $this->info('Today is weekend. No material reminders will be sent.');
            return;
        }

        $now = Carbon::now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();

        /* 
         // TEMPORARILY DISABLED FOR TESTING
         // Check if it's the right time
         $configuredTime = Setting::where('key', 'material_reminder_time')->value('value') ?? '17:00';
         $timeTarget = Carbon::createFromFormat('H:i', $configuredTime);
         // Allow a small window (e.g. 5 mins) to prevent exact minute timing issues if cron is delayed
         if ($now->format('H:i') !== $timeTarget->format('H:i')) {
         $this->info("Current time {$now->format('H:i')} does not match target time {$timeTarget->format('H:i')}. Skipping.");
         return;
         }
         */

        // Find users who should receive the reminder
        // 1. daily_material_reminder_enabled must be true
        // 2. Role must be employee or geselle
        // 3. User must be active
        $usersToRemind = collect();

        $eligibleUsers = User::where('is_active', true)
            ->where('daily_material_reminder_enabled', true)
            ->whereIn('role', ['employee', 'geselle'])
            ->get();

        foreach ($eligibleUsers as $user) {
            // Check if the user has any 'taken' transactions today
            $hasTakenMaterialToday = MaterialTransaction::where('user_id', $user->id)
                ->where('type', 'taken')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->exists();

            if (!$hasTakenMaterialToday) {
                // If they haven't taken anything, send the reminder
                try {
                    Mail::to($user->email)->send(new MaterialReminderMail($user));
                    $this->info("Sent material reminder to: {$user->email}");
                    $usersToRemind->push($user);
                }
                catch (\Exception $e) {
                    $this->error("Exception caught: " . $e->getMessage());
                    $this->error("Trace: " . $e->getTraceAsString());
                    Log::error("Failed to send material reminder to {$user->email}: " . $e->getMessage());
                    $this->error("Failed to send material reminder to {$user->email}");
                }
            }
        }

        $this->info("Finished material reminders. Sent emails to " . $usersToRemind->count() . " users.");
    }
}
