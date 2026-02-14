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

        // Optional: Send email to user (not requested but good practice)

        return back()->with('success', "Nutzer {$user->name} wurde freigeschaltet.");
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
}
