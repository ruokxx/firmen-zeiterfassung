<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $users = \App\Models\User::with(['workDays' => function ($query) {
            $query->orderBy('date', 'desc');
        }])->get();

        // Group workdays by month for each user
        $users->each(function ($user) {
            $user->months = $user->workDays->groupBy(function ($date) {
                    return \Carbon\Carbon::parse($date->date)->format('Y-m');
                }
                );
            });

        $pendingUsers = \App\Models\User::where('is_active', false)->get();

        return view('admin.dashboard', compact('users', 'pendingUsers'));
    }
    public function toggleAdmin(\App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Du kannst dir selbst nicht die Admin-Rechte entziehen.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'zum Admin befördert' : 'Admin-Rechte entzogen';
        return back()->with('success', "Nutzer {$user->name} wurde {$status}.");
    }

    public function approve(\App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $user->is_active = true;
        $user->save();

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AccountApprovedMail($user));
            $message = "Nutzer {$user->name} wurde freigeschaltet und per E-Mail informiert.";
        }
        catch (\Exception $e) {
            $message = "Nutzer {$user->name} wurde freigeschaltet, aber die E-Mail konnte nicht gesendet werden: " . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    public function destroy(\App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Du kannst dich nicht selbst löschen.');
        }

        $user->delete();
        return back()->with('success', "Nutzer {$user->name} wurde gelöscht.");
    }

    public function downloadBackup()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $path = database_path('database.sqlite');
        if (file_exists($path)) {
            return response()->download($path, 'backup_' . date('Y-m-d_H-i-s') . '.sqlite');
        }

        return back()->with('error', 'Datenbankdatei nicht gefunden.');
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
        $path = database_path('database.sqlite');

        // Create a safety backup of existing DB before overwriting
        if (file_exists($path)) {
            copy($path, database_path('database_pre_restore_' . date('Y-m-d_H-i-s') . '.sqlite'));
        }

        // Overwrite
        try {
            copy($file->getRealPath(), $path);
            return back()->with('success', 'Datenbank wurde erfolgreich wiederhergestellt!');
        }
        catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Wiederherstellen: ' . $e->getMessage());
        }
    }
}
