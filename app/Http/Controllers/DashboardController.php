<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // Load configured defaults once per request
        $defaultStart = \App\Models\Setting::where('key', 'default_start_time')->value('value') ?: '08:00';
        $defaultEnd = \App\Models\Setting::where('key', 'default_end_time')->value('value') ?: '16:00';
        $defaultBreak = \App\Models\Setting::where('key', 'default_break_duration')->value('value') !== null
            ? (int)\App\Models\Setting::where('key', 'default_break_duration')->value('value')
            : 0;

        $start = \Carbon\Carbon::parse($defaultStart);
        $end = \Carbon\Carbon::parse($defaultEnd);
        $diffMinutes = $start->diffInMinutes($end);
        $workMinutes = max(0, $diffMinutes - $defaultBreak);
        $defaultDailyHours = round($workMinutes / 60, 2);

        // Get all workdays for the selected year
        $workDays = \App\Models\WorkDay::where('user_id', auth()->id())
            ->whereYear('date', $year)
            ->with('timeEntries.constructionSite')
            ->get();

        // Group by month and calculate totals
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthDate = \Carbon\Carbon::createFromDate($year, $m, 1);

            // Filter workdays for this month
            $monthWorkDays = $workDays->filter(function ($day) use ($m) {
                return \Carbon\Carbon::parse($day->date)->month === $m;
            });

            // Calculate total hours
            // Calculate total hours using the model accessor (handles both entries and start/end times)
            $totalHours = $monthWorkDays->sum(function ($day) {
                return $day->total_hours;
            });

            // Calculate Vacation Days
            $vacationDays = $monthWorkDays->sum(function ($day) {
                return $day->timeEntries->filter(function ($entry) {
                        return $entry->constructionSite && $entry->constructionSite->name === 'Urlaub';
                    }
                    )->sum('hours') / 8;
                });

            // Calculate target hours (8 hours per weekday, adapting to actual if > 8)
            $targetHours = 0;
            $daysInMonth = $monthDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = \Carbon\Carbon::createFromDate($year, $m, $d);
                $dateString = $date->format('Y-m-d');

                // Find work entry for this day
                // Correction: $monthWorkDays is a collection of WorkDay objects.
                // We don't need to re-find it if we iterate correctly, but here we iterate days of month.
                // $dayEntry is unused in the loop logic below anyway?
                // Ah, the logic below checks for weekend. 
                // Wait, the original code had:
                /* 
                 $dayEntry = $monthWorkDays->first(function ($day) use ($dateString) {
                 return \Carbon\Carbon::parse($day->date)->format('Y-m-d') === $dateString;
                 });
                 */
                // But $dayEntry was NOT used in original code for target calculation logic shown in snippet?
                // Let's keep it consistent with what I see in view_file output.
                // Actually, lines 43-45 find $dayEntry but don't use it. I will leave it be or remove it if I replace the whole block.
                // I will just replace the start and end of the block I am touching.

                if (!$date->isWeekend()) {
                    $targetHours += $defaultDailyHours;
                }
            }

            // Get days with entries (only count if they actually have time entries, not just an empty WorkDay record)
            $workedDays = $monthWorkDays->filter(function ($day) {
                return $day->timeEntries->count() > 0;
            })->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            // Get days with vacation entries
            $vacationDates = $monthWorkDays->filter(function ($day) {
                return $day->timeEntries->contains(function ($entry) {
                        return $entry->constructionSite && $entry->constructionSite->name === 'Urlaub';
                    }
                    );
                })->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            // Get days with sick entries
            $sickDates = $monthWorkDays->filter(function ($day) {
                return $day->timeEntries->contains(function ($entry) {
                        return $entry->constructionSite && $entry->constructionSite->name === 'Krank';
                    }
                    );
                })->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            // Get days with "Folgt nächsten Monat" entries
            $folgtDates = $monthWorkDays->filter(function ($day) {
                return $day->timeEntries->contains(function ($entry) {
                        return $entry->constructionSite && $entry->constructionSite->name === 'Folgt nächsten Monat';
                    }
                    );
                })->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            // Get days with "Schule" entries
            $schoolDates = $monthWorkDays->filter(function ($day) {
                return $day->timeEntries->contains(function ($entry) {
                        return $entry->constructionSite && $entry->constructionSite->name === 'Schule';
                    }
                    );
                })->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            // Get days with Holiday entries (starts with "Feiertag:")
            $holidayDates = $monthWorkDays->filter(function ($day) {
                return $day->timeEntries->contains(function ($entry) {
                        return $entry->constructionSite && \Illuminate\Support\Str::startsWith($entry->constructionSite->name, 'Feiertag:');
                    }
                    );
                })->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            $months[$m] = [
                'date' => $monthDate,
                'total_hours' => $totalHours,
                'vacation_days' => $vacationDays,
                'target_hours' => $targetHours,
                'worked_days' => $workedDays,
                'vacation_dates' => $vacationDates,
                'sick_dates' => $sickDates,
                'school_dates' => $schoolDates,
                'folgt_dates' => $folgtDates,
                'holiday_dates' => $holidayDates,
            ];
        }

        // Monate so sortieren, dass der aktuelle Monat zuerst kommt, dann der Folgemonat usw.
        $currentMonth = (int)date('n');
        $sortedMonths = [];

        // Aktueller Monat bis Dezember
        for ($m = $currentMonth; $m <= 12; $m++) {
            $sortedMonths[$m] = $months[$m];
        }

        // Januar bis Vormonat
        for ($m = 1; $m < $currentMonth; $m++) {
            $sortedMonths[$m] = $months[$m];
        }

        $months = $sortedMonths;

        $yearlyTotal = collect($months)->sum('total_hours');

        // Calculate Yearly Progress
        $holidays = $this->getHolidaysNI($year);
        $totalWorkingDays = 0;
        $startDate = \Carbon\Carbon::createFromDate($year, 1, 1);
        $endDate = \Carbon\Carbon::createFromDate($year, 12, 31);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $isHoliday = false;
                foreach ($holidays as $h) {
                    if ($h->isSameDay($date)) {
                        $isHoliday = true;
                        break;
                    }
                }

                if (!$isHoliday) {
                    $totalWorkingDays++;
                }
            }
        }

        $daysWorked = $workDays->filter(function ($day) {
            return $day->total_hours > 0;
        })->count();

        $progressPercentage = $totalWorkingDays > 0 ? ($daysWorked / $totalWorkingDays) * 100 : 0;

        $progressPercentage = $totalWorkingDays > 0 ? ($daysWorked / $totalWorkingDays) * 100 : 0;

        // Fetch Team List
        $team = \App\Models\User::orderBy('name')->where('is_active', true)->get();

        $hasLowStock = \App\Models\Material::whereColumn('stock_count', '<=', 'low_stock_threshold')->exists();

        return view('dashboard', compact('year', 'months', 'yearlyTotal', 'daysWorked', 'totalWorkingDays', 'progressPercentage', 'team', 'hasLowStock'));
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
