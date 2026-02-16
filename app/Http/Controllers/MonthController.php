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

        return view('month', compact('year', 'month', 'startOfMonth', 'daysInMonth', 'workDays', 'calendarDays'));
    }
}
