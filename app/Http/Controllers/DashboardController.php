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
            $totalHours = $monthWorkDays->sum(function ($day) {
                return $day->timeEntries->sum('hours');
            });

            // Get days with entries
            $workedDays = $monthWorkDays->map(function ($day) {
                return (int)\Carbon\Carbon::parse($day->date)->day;
            })->toArray();

            $months[$m] = [
                'date' => $monthDate,
                'total_hours' => $totalHours,
                'worked_days' => $workedDays,
            ];
        }

        $yearlyTotal = collect($months)->sum('total_hours');

        return view('dashboard', compact('year', 'months', 'yearlyTotal'));
    }
}
