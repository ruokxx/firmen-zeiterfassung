<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically create a database backup if enabled in settings.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $enabled = \App\Models\Setting::where('key', 'auto_backup_enabled')->value('value') === '1';

        if (!$enabled) {
            $this->info('Automatic backup is disabled.');
            return;
        }

        $this->info('Starting automatic backup...');

        try {
            $service = new \App\Services\BackupService();
            $filename = $service->createBackup();
            $this->info("Backup created: $filename");

            $retention = (int)(\App\Models\Setting::where('key', 'backup_retention_count')->value('value') ?? 5);
            $deleted = $service->cleanupBackups($retention);

            if ($deleted > 0) {
                $this->info("Cleaned up $deleted old backups.");
            }

        }
        catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            \Illuminate\Support\Facades\Log::error("Auto Backup Failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
