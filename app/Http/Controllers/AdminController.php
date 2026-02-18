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
    public function updateRole(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Du kannst deine eigene Rolle nicht ändern.');
        }

        $request->validate([
            'role' => 'required|in:employee,chef,admin',
        ]);

        // Only Super Admins can make other Admins
        if ($request->role === 'admin' && !auth()->user()->is_super_admin) {
            return back()->with('error', 'Nur Super-Admins können Datenbank-Admins ernennen.');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', "Rolle von {$user->name} wurde auf {$request->role} geändert.");
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


    public function email(\App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        return view('admin.users.email', compact('user'));
    }

    public function sendEmail(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('temp_attachments');
        }

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\AdminMessageMail($request->subject, $request->message, $attachmentPath ? storage_path('app/' . $attachmentPath) : null));

            if ($attachmentPath) {
                \Illuminate\Support\Facades\Storage::delete($attachmentPath);
            }

            return redirect()->route('admin.dashboard')->with('success', "Email an {$user->name} wurde gesendet.");
        }
        catch (\Exception $e) {
            if ($attachmentPath) {
                \Illuminate\Support\Facades\Storage::delete($attachmentPath);
            }
            return back()->with('error', "Fehler beim Senden der Email: " . $e->getMessage())->withInput();
        }
    }
}
