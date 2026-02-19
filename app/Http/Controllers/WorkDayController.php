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
            'start_time' => null,
            'end_time' => null,
            'break_duration' => null
        ]
        );

        $workDay->load('timeEntries.constructionSite');

        $sites = \App\Models\ConstructionSite::where('status', 'active')->get();

        return view('workday.edit', compact('workDay', 'sites'));
    }

    public function setStatus(Request $request, $date)
    {
        $status = $request->input('status'); // 'Krank', 'Urlaub' or 'Folgt nächsten Monat'
        if (!in_array($status, ['Krank', 'Urlaub', 'Folgt nächsten Monat'])) {
            return back()->with('error', 'Ungültiger Status.');
        }

        $user = auth()->user();

        // Find or create WorkDay with standard 8h times
        $workDay = $user->workDays()->updateOrCreate(
        ['date' => $date],
        [
            'start_time' => '08:00',
            'end_time' => '16:00',
            'break_duration' => 0
        ]
        );

        // Clear existing entries
        $workDay->timeEntries()->delete();

        // Create specific "Site"
        $site = \App\Models\ConstructionSite::firstOrCreate(
        ['name' => $status],
        ['status' => 'active']
        );

        // Add 8h entry
        $workDay->timeEntries()->create([
            'construction_site_id' => $site->id,
            'hours' => 8
        ]);

        return back()->with('success', "$status für $date wurde eingetragen.");
    }

    public function saveAjax(Request $request)
    {
        try {
            $user = auth()->user();

            $input = $request->all();
            // Convert empty strings to null
            $input['start_time'] = $input['start_time'] ?: null;
            $input['end_time'] = $input['end_time'] ?: null;
            $input['break_duration'] = ($input['break_duration'] === '' || $input['break_duration'] === null) ? null : $input['break_duration'];

            // Validate basic inputs (use $input instead of $request->all())
            $validator = \Illuminate\Support\Facades\Validator::make($input, [
                'date' => 'required|date',
                'start_time' => 'nullable',
                'end_time' => 'nullable',
                'break_duration' => 'nullable|integer|min:0',
                'entries' => 'array',
                'entries.*.construction_site_name' => 'nullable|string',
                'entries.*.hours' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validierungsfehler',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // Find or create WorkDay
            $workDay = $user->workDays()->firstOrCreate(
            ['date' => $validated['date']],
            [
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'break_duration' => $validated['break_duration'] ?? null
            ]
            );

            // Update WorkDay details
            $workDay->update([
                'start_time' => $validated['start_time'] ?? null,
                'end_time' => $validated['end_time'] ?? null,
                'break_duration' => $validated['break_duration'] ?? null
            ]);

            // Sync Entries
            $workDay->timeEntries()->delete();

            if (!empty($validated['entries'])) {
                foreach ($validated['entries'] as $entry) {
                    if (isset($entry['hours']) && $entry['hours'] > 0 && !empty($entry['construction_site_name'])) {
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

            return response()->json(['success' => true, 'message' => 'Gespeichert!']);

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Ajax Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, \App\Models\WorkDay $workDay)
    {
        try {
            if ($workDay->user_id !== auth()->id()) {
                abort(403);
            }

            $data = $request->all();

            $validator = \Illuminate\Support\Facades\Validator::make($data, [
                'start_time' => 'nullable',
                'end_time' => 'nullable',
                'break_duration' => 'nullable|integer|min:0',
                'entries' => 'array',
                'entries.*.construction_site_name' => 'nullable|string',
                'entries.*.hours' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                if ($this->isAjax($request)) {
                    return response()->json(['success' => false, 'message' => 'Validierungsfehler', 'errors' => $validator->errors()], 422);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $validated = $validator->validated();

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

            if ($this->isAjax($request)) {
                return response()->json(['success' => true, 'message' => 'Gespeichert!']);
            }

            return redirect()->route('month.show', [
                'year' => \Carbon\Carbon::parse($workDay->date)->year,
                'month' => \Carbon\Carbon::parse($workDay->date)->month
            ])->with('success', 'Gespeichert!');

        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Workday update error: ' . $e->getMessage());

            if ($this->isAjax($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fehler: ' . $e->getMessage()
                ], 500);
            }

            throw $e;
        }
    }

    public function destroy(Request $request, $date)
    {
        $user = auth()->user();
        $workDay = $user->workDays()->where('date', $date)->first();

        if ($workDay) {
            $workDay->timeEntries()->delete();
            $workDay->delete();
            return back()->with('success', 'Einträge für ' . $date . ' wurden zurückgesetzt.');
        }

        return back()->with('error', 'Keine Einträge gefunden.');
    }

    private function isAjax(Request $request)
    {
        return $request->wantsJson() || $request->ajax() || $request->has('is_ajax');
    }
}
