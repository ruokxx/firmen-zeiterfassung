<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\WorkDay;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Fetch available report months
        $reports = WorkDay::where('user_id', $request->user()->id)
            ->selectRaw('strftime("%Y", date) as year, strftime("%m", date) as month') // SQLite compatible for tests, works in MySQL too mostly or uses YEAR/MONTH
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // If the above SQL is too specific (SQLite vs MySQL), we can use a collection approach for safety if dataset isn't huge.
        // But for now, let's try a safer cross-driver approach or just fetch all and unique.
        // Actually, let's stick to standard SQL or Collection for maximum safety given the environment.

        $reports = WorkDay::where('user_id', $request->user()->id)
            ->get()
            ->groupBy(function ($date) {
            return Carbon::parse($date->date)->format('Y-m');
        })
            ->map(function ($days, $key) {
            $date = Carbon::createFromFormat('Y-m', $key);
            return [
            'year' => $date->year,
            'month' => $date->month,
            'label' => $date->locale('de')->isoFormat('MMMM YYYY'),
            ];
        })
            ->values();

        return view('profile.edit', [
            'user' => $request->user(),
            'reports' => $reports,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        abort(403, 'Account deletion is disabled for users.');
    }
}
