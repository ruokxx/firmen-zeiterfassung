<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\MaterialTransaction;
use App\Mail\DailyMaterialReportMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendDailyMaterialReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materials:send-daily-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the daily material withdrawal report to the configured admin email.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Daily Material Report job...');

        // Check if materials feature is enabled system-wide
        $materialsEnabled = Setting::where('key', 'materials_enabled')->value('value') !== '0';
        if (!$materialsEnabled) {
            $this->info('Materials feature is system-wide disabled. Aborting.');
            return 0;
        }

        // 1. Check if report is enabled
        $isEnabled = Setting::where('key', 'material_daily_report_enabled')->value('value') === '1';

        if (!$isEnabled) {
            $this->info('Daily material report is explicitly disabled in settings. Aborting.');
            return;
        }

        // 2. Determine target email address
        $targetEmail = Setting::where('key', 'low_stock_email_address')->value('value');

        if (empty($targetEmail)) {
            $this->warn('No email address configured for warnings/reports. Cannot send the report.');
            return;
        }

        // 3. Fetch today's transactions roughly
        $todayStr = Carbon::today()->format('d.m.Y');
        $transactions = MaterialTransaction::with(['user', 'material'])
            ->where('type', 'taken')
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        // 4. Send Email
        try {
            Mail::to($targetEmail)->send(new DailyMaterialReportMail($transactions, $todayStr));
            $this->info('Successfully sent daily material report to ' . $targetEmail);
        }
        catch (\Exception $e) {
            Log::error('Failed to send daily material report: ' . $e->getMessage());
            $this->error('Failed to send mail: ' . $e->getMessage());
        }
    }
}
