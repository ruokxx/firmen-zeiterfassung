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

        return view('month', compact('year', 'month', 'startOfMonth', 'daysInMonth', 'workDays'));
    }
}
