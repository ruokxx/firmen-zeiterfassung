<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        // List Backups
        $backups = [];
        // Ensure directory exists
        if (!Storage::exists('backups')) {
            Storage::makeDirectory('backups');
        }

        $files = Storage::files('backups');
        foreach ($files as $file) {
            // Filter for sqlite files
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sqlite') {
                $backups[] = [
                    'filename' => basename($file),
                    'path' => $file,
                    'size' => Storage::size($file),
                    'last_modified' => Storage::lastModified($file),
                ];
            }
        }

        // Sort by date desc
        usort($backups, function ($a, $b) {
            return $b['last_modified'] <=> $a['last_modified'];
        });

        return view('admin.settings', compact('settings', 'backups'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
            'boss_email' => 'nullable|email',
            'monthly_report_subject' => 'nullable|string',
            'monthly_report_body' => 'nullable|string',
            'account_approved_subject' => 'nullable|string',
            'account_approved_body' => 'nullable|string',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'E-Mail Einstellungen erfolgreich gespeichert.');
    }

    // --- Backup Methods ---

    public function generateBackup()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        // Check compatibility
        $connection = \Illuminate\Support\Facades\DB::connection();
        if ($connection->getDriverName() !== 'sqlite') {
            return back()->with('error', 'Backup-Funktion ist derzeit nur für SQLite-Datenbanken verfügbar.');
        }

        // Get active database path dynamically
        $sourcePath = $connection->getDatabaseName();
        \Illuminate\Support\Facades\Log::info('Backup Process Started. Source: ' . $sourcePath);

        if (!file_exists($sourcePath)) {
            \Illuminate\Support\Facades\Log::error('Backup Failed: Source file not found at ' . $sourcePath);
            return back()->with('error', 'Quelldatenbank nicht gefunden: ' . $sourcePath);
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sqlite';
        \Illuminate\Support\Facades\Log::info('Backup Target Filename: ' . $filename);

        // Use Storage facade to put file
        try {
            // Create backup using file copy logic
            $content = file_get_contents($sourcePath);
            if ($content === false) {
                \Illuminate\Support\Facades\Log::error('Backup Failed: Could not read source file.');
                throw new \Exception('Could not read source file.');
            }

            Storage::put('backups/' . $filename, $content);

            if (Storage::exists('backups/' . $filename)) {
                \Illuminate\Support\Facades\Log::info('Backup Success: File exists in storage.');
            }
            else {
                \Illuminate\Support\Facades\Log::error('Backup Failed: File does not exist after put.');
            }

            return back()->with('success', 'Backup erfolgreich erstellt: ' . $filename);
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Backup Exception: ' . $e->getMessage());
            return back()->with('error', 'Fehler beim Erstellen des Backups: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if (!Storage::exists('backups/' . $filename)) {
            return back()->with('error', 'Backup-Datei nicht gefunden.');
        }

        return Storage::download('backups/' . $filename);
    }

    public function restoreBackup(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'backup_file' => 'required|file'
        ]);

        $file = $request->file('backup_file');
        $destPath = database_path('database.sqlite');

        // Create a safety backup of existing DB before overwriting
        if (file_exists($destPath)) {
            // Copy to storage/backups as a pre-restore safety
            $safetyName = 'auto_backup_pre_restore_' . date('Y-m-d_H-i-s') . '.sqlite';
            Storage::put('backups/' . $safetyName, file_get_contents($destPath));
        }

        // Overwrite
        try {
            copy($file->getRealPath(), $destPath);
            return back()->with('success', 'Datenbank wurde erfolgreich wiederhergestellt!');
        }
        catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Wiederherstellen: ' . $e->getMessage());
        }
    }

    public function deleteBackup($filename)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if (Storage::exists('backups/' . $filename)) {
            Storage::delete('backups/' . $filename);
            return back()->with('success', 'Backup gelöscht.');
        }

        return back()->with('error', 'Datei nicht gefunden.');
    }
}
