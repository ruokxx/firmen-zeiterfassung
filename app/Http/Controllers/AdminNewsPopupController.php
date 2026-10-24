<?php

namespace App\Http\Controllers;

use App\Models\NewsPopup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminNewsPopupController extends Controller
{
    public function index()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $popups = NewsPopup::withCount('reads')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalUsersCount = User::where('is_active', true)->count();

        return view('admin.news_popups.index', compact('popups', 'totalUsersCount'));
    }

    public function create()
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        return view('admin.news_popups.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,important,warning,success',
            'duration_preset' => 'required|in:1_day,2_days,3_days,7_days,custom,no_limit',
            'expires_at' => 'nullable|date',
            'display_type' => 'required|in:once_per_user,once_per_day,always',
            'is_active' => 'nullable|boolean',
        ]);

        $startAt = now();
        $expiresAt = null;

        switch ($validated['duration_preset']) {
            case '1_day':
                $expiresAt = now()->addDay();
                break;
            case '2_days':
                $expiresAt = now()->addDays(2);
                break;
            case '3_days':
                $expiresAt = now()->addDays(3);
                break;
            case '7_days':
                $expiresAt = now()->addDays(7);
                break;
            case 'custom':
                $expiresAt = $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : null;
                break;
            case 'no_limit':
                $expiresAt = null;
                break;
        }

        NewsPopup::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'start_at' => $startAt,
            'expires_at' => $expiresAt,
            'display_type' => $validated['display_type'],
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.news-popups.index')->with('success', 'News-Popup erfolgreich erstellt.');
    }

    public function edit(NewsPopup $newsPopup)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        return view('admin.news_popups.edit', compact('newsPopup'));
    }

    public function update(Request $request, NewsPopup $newsPopup)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,important,warning,success',
            'duration_preset' => 'required|in:1_day,2_days,3_days,7_days,custom,no_limit,keep',
            'expires_at' => 'nullable|date',
            'display_type' => 'required|in:once_per_user,once_per_day,always',
            'is_active' => 'nullable|boolean',
        ]);

        $expiresAt = $newsPopup->expires_at;

        if ($validated['duration_preset'] !== 'keep') {
            switch ($validated['duration_preset']) {
                case '1_day':
                    $expiresAt = now()->addDay();
                    break;
                case '2_days':
                    $expiresAt = now()->addDays(2);
                    break;
                case '3_days':
                    $expiresAt = now()->addDays(3);
                    break;
                case '7_days':
                    $expiresAt = now()->addDays(7);
                    break;
                case 'custom':
                    $expiresAt = $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : null;
                    break;
                case 'no_limit':
                    $expiresAt = null;
                    break;
            }
        } elseif ($request->filled('expires_at')) {
            $expiresAt = Carbon::parse($request->input('expires_at'));
        }

        $newsPopup->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'type' => $validated['type'],
            'expires_at' => $expiresAt,
            'display_type' => $validated['display_type'],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.news-popups.index')->with('success', 'News-Popup erfolgreich aktualisiert.');
    }

    public function toggle(NewsPopup $newsPopup)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $newsPopup->is_active = !$newsPopup->is_active;
        $newsPopup->save();

        return back()->with('success', 'Status für Popup "' . $newsPopup->title . '" aktualisiert.');
    }

    public function destroy(NewsPopup $newsPopup)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $newsPopup->delete();

        return redirect()->route('admin.news-popups.index')->with('success', 'News-Popup gelöscht.');
    }
}
