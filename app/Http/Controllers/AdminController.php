<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = \App\Models\User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        $users = $query->with(['workDays' => function ($q) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $q->whereDate('date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $q->whereDate('date', '<=', $dateTo);
            }
            $q->orderBy('date', 'desc')->with('timeEntries.constructionSite');
        }])->get();

        // Group workdays by month for each user
        $users->each(function ($user) {
            $user->months = $user->workDays->groupBy(function ($date) {
                    return \Carbon\Carbon::parse($date->date)->format('Y-m');
                }
                );
            });

        $pendingUsers = \App\Models\User::where('is_active', false)->get();

        return view('admin.dashboard', compact('users', 'pendingUsers', 'search', 'dateFrom', 'dateTo'));
    }
    public function updateRole(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->is_admin && !auth()->user()->is_chef) {
            abort(403);
        }

        $request->validate([
            'role' => 'required|in:employee,chef,admin,azubi,geselle',
        ]);

        // If the user being modified is a super admin, and their new rights are NOT super admin,
        // we must ensure there is at least one OTHER super admin left in the system.
        if ($user->is_super_admin && !$request->has('is_super_admin')) {
            $superAdminCount = \App\Models\User::where('is_super_admin', true)->count();
            if ($superAdminCount <= 1) {
                return back()->with('error', 'Fehler: Es muss mindestens ein Super-Admin im System verbleiben!');
            }
        }

        // Only Super Admins can make other Super Admins or Admins
        if (($request->has('is_admin') || $request->has('is_super_admin')) && !auth()->user()->is_super_admin && $user->id !== auth()->id()) {
            // A normal admin cannot grant admin rights to someone else
            // Except they can keep their own admin rights if they are just changing their role name
            return back()->with('error', 'Nur Super-Admins können Admin-Rechte vergeben.');
        }

        $user->role = $request->role;
        $user->is_materialwart = $request->has('is_materialwart');

        // Handle admin flags
        if (auth()->user()->is_super_admin || $user->id === auth()->id()) {
            $user->is_admin = $request->has('is_admin');

            // Only super admins can grant/revoke super admin rights
            if (auth()->user()->is_super_admin) {
                $user->is_super_admin = $request->has('is_super_admin');
            }
        }

        $user->save();

        return back()->with('success', "Daten von {$user->name} wurden erfolgreich aktualisiert.");
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

    public function updateVacationDays(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'vacation_days_per_year' => 'nullable|integer|min:0',
        ]);

        $user->vacation_days_per_year = $request->vacation_days_per_year;
        $user->save();

        return back()->with('success', "Individuelle Urlaubstage für {$user->name} gespeichert.");
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


    public function editUser(\App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, \App\Models\User $user)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'mobile_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'daily_material_reminder_enabled' => 'nullable|boolean',
            'daily_reminder_enabled' => 'nullable|boolean',
        ]);

        $validatedData['daily_material_reminder_enabled'] = $request->has('daily_material_reminder_enabled');
        $validatedData['daily_reminder_enabled'] = $request->has('daily_reminder_enabled');

        $user->update($validatedData);

        return redirect()->route('admin.dashboard')->with('success', "Nutzer {$user->name} wurde erfolgreich aktualisiert.");
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
