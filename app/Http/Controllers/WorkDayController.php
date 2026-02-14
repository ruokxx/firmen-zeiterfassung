<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkDayController extends Controller
{
    public function edit($date)
    {
        $user = auth()->user();
        $workDay = $user->workDays()->firstOrCreate(
        ['date' => $date],
        [
            'start_time' => '08:00',
            'end_time' => '16:30',
            'break_duration' => 30
        ]
        );

        $workDay->load('timeEntries.constructionSite');

        $sites = \App\Models\ConstructionSite::where('status', 'active')->get();

        return view('workday.edit', compact('workDay', 'sites'));
    }

    public function update(Request $request, \App\Models\WorkDay $workDay)
    {
        if ($workDay->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'start_time' => 'required',
            'end_time' => 'required',
            'break_duration' => 'required|integer',
            'entries' => 'array',
            'entries.*.construction_site_name' => 'required|string',
            'entries.*.hours' => 'required|numeric|min:0.5',
        ]);

        $workDay->update([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'break_duration' => $validated['break_duration'],
        ]);

        // Sync entries
        $workDay->timeEntries()->delete();

        if (!empty($validated['entries'])) {
            foreach ($validated['entries'] as $entry) {
                if ($entry['hours'] > 0 && !empty($entry['construction_site_name'])) {

                    $site = \App\Models\ConstructionSite::firstOrCreate(
                    ['name' => $entry['construction_site_name']],
                    ['status' => 'active']
                    );

                    $workDay->timeEntries()->create([
                        'construction_site_id' => $site->id,
                        'hours' => $entry['hours'],
                    ]);
                }
            }
        }

        return redirect()->route('month.show', [
            'year' => \Carbon\Carbon::parse($workDay->date)->year,
            'month' => \Carbon\Carbon::parse($workDay->date)->month
        ])->with('success', 'Gespeichert!');
    }
}
