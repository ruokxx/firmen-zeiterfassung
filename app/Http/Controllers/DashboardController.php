<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', date('Y'));

        // Get all workdays for the selected year
        $workDays = \App\Models\WorkDay::where('user_id', auth()->id())
            ->whereYear('date', $year)
            ->with('timeEntries')
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

            // Calculate target hours (8 hours per weekday, adapting to actual if > 8)
            $targetHours = 0;
            $daysInMonth = $monthDate->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $date = \Carbon\Carbon::createFromDate($year, $m, $d);
                $dateString = $date->format('Y-m-d');

                // Find work entry for this day
                $dayEntry = $monthWorkDays->first(function ($day) use ($dateString) {
                    return \Carbon\Carbon::parse($day->date)->format('Y-m-d') === $dateString;
                });

                if (!$date->isWeekend()) {
                    $targetHours += 8;
                }
            }

            // Get days with entries
            $workedDays = $monthWorkDays->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            $months[$m] = [
                'date' => $monthDate,
                'total_hours' => $totalHours,
                'target_hours' => $targetHours,
                'worked_days' => $workedDays,
            ];
        }


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

        return view('dashboard', compact('year', 'months', 'yearlyTotal', 'daysWorked', 'totalWorkingDays', 'progressPercentage', 'team'));
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
