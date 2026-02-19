<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonthController extends Controller
{
    public function show($year, $month)
    {
        $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $startOfMonth->daysInMonth;

        $workDays = \App\Models\WorkDay::where('user_id', auth()->id())
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with(['timeEntries.constructionSite'])
            ->get()
            ->keyBy('date');

        // Generate all days of the month
        $calendarDays = collect();
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $calendarDays->push($startOfMonth->copy()->addDays($day - 1));
        }

        // If it's the current month, rotate the collection so today is first
        if ($startOfMonth->isCurrentMonth()) {
            $today = \Carbon\Carbon::today();
            // Find the index of today (day of month - 1)
            $dayIndex = $today->day - 1;

            // Rotate: slice from today to end, then slice from start to today
            $part1 = $calendarDays->slice($dayIndex);
            $part2 = $calendarDays->slice(0, $dayIndex);
            $calendarDays = $part1->merge($part2);
        }

        $totalHours = $workDays->sum(function ($day) {
            return $day->timeEntries->sum('hours');
        });

        // Calculate Target Hours
        $targetHours = 0;
        $holidays = $this->getHolidaysNI($year);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $startOfMonth->copy()->day($day);

            if ($date->isWeekday()) {
                $isHoliday = false;
                foreach ($holidays as $h) {
                    if ($h->isSameDay($date)) {
                        $isHoliday = true;
                        break;
                    }
                }

                if (!$isHoliday) {
                    $targetHours += 8;
                }
            }
        }

        return view('month', compact('year', 'month', 'startOfMonth', 'daysInMonth', 'workDays', 'calendarDays', 'totalHours', 'targetHours'));
    }

    public function importHolidays(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');

        if (!$year || !$month) {
            return back()->with('error', 'Ungültiges Datum.');
        }

        // Calculate Holidays for Niedersachsen (NI)
        $holidays = $this->getHolidaysNI($year);

        $user = auth()->user();
        $count = 0;

        foreach ($holidays as $name => $date) {
            // $date is Carbon object

            // Check if holiday is in the requested month
            if ($date->year == $year && $date->month == $month) {

                // Check if it's a weekday (Monday - Friday)
                if ($date->isWeekday()) {
                    $dateString = $date->format('Y-m-d');

                    // Check if entry already exists for this user and date
                    $exists = $user->workDays()->where('date', $dateString)->exists();

                    if (!$exists) {
                        // Create WorkDay
                        $workDay = $user->workDays()->create([
                            'date' => $dateString,
                            'start_time' => '08:00',
                            'end_time' => '16:00',
                            'break_duration' => 0
                        ]);

                        // Create/Find "Feiertag" Site
                        $site = \App\Models\ConstructionSite::firstOrCreate(
                        ['name' => "Feiertag: $name"],
                        ['status' => 'active']
                        );

                        // Add Entry
                        $workDay->timeEntries()->create([
                            'construction_site_id' => $site->id,
                            'hours' => 8
                        ]);

                        $count++;
                    }
                }
            }
        }

        return back()->with('success', "$count Feiertage wurden importiert.");
    }

    private function getHolidaysNI($year)
    {
        // Use easter_days to avoid timezone issues with easter_date
        $daysSinceMarch21 = easter_days($year);
        $easter = \Carbon\Carbon::createFromDate($year, 3, 21)->addDays($daysSinceMarch21);

        $holidays = [
            'Neujahr' => \Carbon\Carbon::createFromDate($year, 1, 1),
            'Karfreitag' => $easter->copy()->subDays(2),
            'Ostermontag' => $easter->copy()->addDays(1),
            'Tag der Arbeit' => \Carbon\Carbon::createFromDate($year, 5, 1),
            'Christi Himmelfahrt' => $easter->copy()->addDays(39),
            'Pfingstmontag' => $easter->copy()->addDays(50),
            'Tag der Deutschen Einheit' => \Carbon\Carbon::createFromDate($year, 10, 3),
            'Reformationstag' => \Carbon\Carbon::createFromDate($year, 10, 31),
            '1. Weihnachtstag' => \Carbon\Carbon::createFromDate($year, 12, 25),
            '2. Weihnachtstag' => \Carbon\Carbon::createFromDate($year, 12, 26),
        ];

        return $holidays;
    }
}
