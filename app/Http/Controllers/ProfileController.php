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

        // Fetch Trello boards if connected
        $trelloBoards = [];
        if ($request->user()->trello_token) {
            try {
                $response = \Illuminate\Support\Facades\Http::get('https://api.trello.com/1/members/me/boards', [
                    'key' => config('services.trello.client_id'), // Ensure this is set in services.php
                    'token' => $request->user()->trello_token,
                    'fields' => 'name,url,shortUrl,prefs',
                ]);

                if ($response->successful()) {
                    $trelloBoards = $response->json();
                }
            }
            catch (\Exception $e) {
            // Ignore
            }
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'reports' => $reports,
            'trelloBoards' => $trelloBoards,
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
    /**
     * Delete all work entries for a specific month.
     */
    public function clearMonth(Request $request): RedirectResponse
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $deleted = WorkDay::where('user_id', $request->user()->id)
            ->whereYear('date', $request->year)
            ->whereMonth('date', $request->month)
            ->delete();

        if ($deleted) {
            return Redirect::back()->with('success', 'Monat wurde erfolgreich geleert.');
        }

        return Redirect::back()->with('error', 'Keine Einträge für diesen Monat gefunden.');
    }
}
