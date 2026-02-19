<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDailyReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email reminders to users who have not logged 8 hours today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Only run on weekdays (Mon-Fri)
        if (!now()->isWeekday()) {
            $this->info('Today is not a weekday. Skipping reminders.');
            return;
        }

        $users = \App\Models\User::where('is_active', true)
            ->where('daily_reminder_enabled', true)
            ->get();

        $today = now()->format('Y-m-d');
        $count = 0;

        foreach ($users as $user) {
            // Get today's hours
            $workDay = $user->workDays()->where('date', $today)->first();
            $hours = $workDay ? $workDay->timeEntries->sum('hours') : 0;

            if ($hours < 8) {
                // Send Email
                \Illuminate\Support\Facades\Mail::to($user)->send(new \App\Mail\DailyReminderMail($user));
                $count++;
            }
        }

        $this->info("Sent {$count} reminders.");
    }
}
