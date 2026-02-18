<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupService
{
    public function createBackup()
    {
        // Ensure directory exists
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $filename = 'backup_' . date('Y-m-d_H-i-s');

        Log::info("Backup Process Started via Service. Driver: " . $driver);

        try {
            if ($driver === 'sqlite') {
                $filename .= '.sqlite';
                $sourcePath = $connection->getDatabaseName();

                if (!file_exists($sourcePath)) {
                    throw new \Exception("Quelldatenbank nicht gefunden: " . $sourcePath);
                }

                $content = file_get_contents($sourcePath);
                if ($content === false) {
                    throw new \Exception("Konnte Quelldatenbank nicht lesen.");
                }

                Storage::put('backups/' . $filename, $content);

            }
            elseif ($driver === 'mysql') {
                $filename .= '.sql';

                $username = config('database.connections.mysql.username');
                $password = config('database.connections.mysql.password');
                $host = config('database.connections.mysql.host');
                $database = config('database.connections.mysql.database');
                $port = config('database.connections.mysql.port');

                // Create a temporary file
                $tempFile = tempnam(sys_get_temp_dir(), 'backup_');

                // Build mysqldump command
                // Note: Using --no-tablespaces to avoid permission issues without SUPER privilege
                $command = sprintf(
                    'mysqldump --user=%s --password=%s --host=%s --port=%s --no-tablespaces %s > %s',
                    escapeshellarg($username),
                    escapeshellarg($password),
                    escapeshellarg($host),
                    escapeshellarg($port),
                    escapeshellarg($database),
                    escapeshellarg($tempFile)
                );

                Log::info("Executing mysqldump");

                $output = [];
                $returnVar = 0;
                exec($command, $output, $returnVar);

                if ($returnVar !== 0) {
                    // Try to capture stderr if possible or just log failure
                    Log::error("mysqldump failed with return code $returnVar");
                    if (file_exists($tempFile))
                        unlink($tempFile);
                    throw new \Exception("Datenbank-Dump fehlgeschlagen (Code $returnVar).");
                }

                $content = file_get_contents($tempFile);
                Storage::put('backups/' . $filename, $content);

                // Cleanup
                if (file_exists($tempFile))
                    unlink($tempFile);

            }
            else {
                throw new \Exception("Datenbanktreiber '$driver' wird nicht unterstützt.");
            }

            // Verify success
            if (Storage::exists('backups/' . $filename)) {
                Log::info('Backup Success: File created.');
                return $filename;
            }
            else {
                throw new \Exception("Datei wurde nach dem Erstellen nicht im Speicher gefunden.");
            }

        }
        catch (\Exception $e) {
            Log::error('Backup Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    public function cleanupBackups($keep = 5)
    {
        $files = Storage::files('backups');
        $backups = [];

        foreach ($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            if (in_array($ext, ['sqlite', 'sql'])) {
                $backups[] = [
                    'path' => $file,
                    'timestamp' => Storage::lastModified($file),
                ];
            }
        }

        // Sort by timestamp desc (newest first)
        usort($backups, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        // If we have more than $keep, delete the rest
        if (count($backups) > $keep) {
            $toDelete = array_slice($backups, $keep);
            foreach ($toDelete as $backup) {
                Storage::delete($backup['path']);
                Log::info("Deleted old backup: " . $backup['path']);
            }
            return count($toDelete);
        }

        return 0;
    }
}
