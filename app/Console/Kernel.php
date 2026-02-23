<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('backup:auto')->dailyAt('22:00');
        $schedule->command('reminders:send-daily')->weekdays()->at('18:00');

        try {
            // Attempt to get the configured time from the database
            $time = \App\Models\Setting::where('key', 'material_email_time')->value('value') ?? '08:00';
            $schedule->command('material-orders:send')->dailyAt($time);
        }
        catch (\Exception $e) {
            // If DB is not available (e.g., during migrations), fallback
            $schedule->command('material-orders:send')->dailyAt('08:00');
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
