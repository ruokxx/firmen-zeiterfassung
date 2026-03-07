<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function download(Request $request)
    {
        $year = (int)$request->input('year', date('Y'));
        $month = (int)$request->input('month', date('n'));

        $userId = $request->input('user_id');
        $currentUser = auth()->user();

        if ($userId && $currentUser->is_admin) {
            $user = \App\Models\User::findOrFail($userId);
        }
        else {
            $user = $currentUser;
        }

        $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1);

        $workDays = $user->workDays()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with(['timeEntries.constructionSite'])
            ->orderBy('date')
            ->get(); // Fixed missing semicolon here or after ->with if get was missing

        // Load configured defaults once per request
        $defaultStart = \App\Models\Setting::where('key', 'default_start_time')->value('value') ?: '08:00';
        $defaultEnd = \App\Models\Setting::where('key', 'default_end_time')->value('value') ?: '16:00';
        $defaultBreak = \App\Models\Setting::where('key', 'default_break_duration')->value('value') !== null
            ? (int)\App\Models\Setting::where('key', 'default_break_duration')->value('value')
            : 0;

        $startTimeParse = \Carbon\Carbon::parse($defaultStart);
        $endTimeParse = \Carbon\Carbon::parse($defaultEnd);
        $diffMinutes = $startTimeParse->diffInMinutes($endTimeParse);
        $workMinutes = $diffMinutes; // Ignore break duration for target calculations
        $defaultDailyHours = round($workMinutes / 60, 2);

        $targetHoursMonth = 0;
        $daysInMonth = $startOfMonth->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = \Carbon\Carbon::createFromDate($year, $month, $d);
            $dateString = $date->format('Y-m-d');

            $dayEntry = $workDays->first(function ($day) use ($dateString) {
                return \Carbon\Carbon::parse($day->date)->format('Y-m-d') === $dateString;
            });

            if (!$date->isWeekend()) {
                $targetHoursMonth += $defaultDailyHours;
            }
        }

        // This variable is needed for the footer calculation in blade if we want to be consistent,
        // but the blade currently only uses $previousMonthBalance. 
        // Checks show blade uses $totalHoursMonth + $previousMonthBalance. 
        // It doesn't display "Target Current" in footer, but for correctness of "Overtime" calculation if added later.

        // Check if carryover should be included (default: false)
        // Explicitly cast to boolean to be safe (though '0' is false, filter_var is clearer)
        $includeCarryover = filter_var($request->input('include_carryover', 'false'), FILTER_VALIDATE_BOOLEAN);
        $previousMonthBalance = 0;

        if ($includeCarryover) {
            $previousMonthDate = $startOfMonth->copy()->subMonth();
            $prevYear = $previousMonthDate->year;
            $prevMonth = $previousMonthDate->month;

            // Calculate Previous Month Balance
            // 1. Get Actual Hours
            $prevMonthWorkDays = $user->workDays()
                ->whereYear('date', $prevYear)
                ->whereMonth('date', $prevMonth)
                ->with('timeEntries')
                ->get();

            $prevActualHours = $prevMonthWorkDays->sum(function ($day) {
                return $day->timeEntries->sum('hours');
            });

            // 2. Calculate Target Hours (8h / weekday, dynamic adjustment)
            $prevTargetHours = 0;
            $daysInPrevMonth = $previousMonthDate->daysInMonth;
            for ($d = 1; $d <= $daysInPrevMonth; $d++) {
                $date = \Carbon\Carbon::createFromDate($prevYear, $prevMonth, $d);
                $dateString = $date->format('Y-m-d');

                $dayEntry = $prevMonthWorkDays->first(function ($day) use ($dateString) {
                    return \Carbon\Carbon::parse($day->date)->format('Y-m-d') === $dateString;
                });

                if (!$date->isWeekend()) {
                    $prevTargetHours += $defaultDailyHours;
                }
            }

            $previousMonthBalance = $prevActualHours - $prevTargetHours;
        }



        // Vacation Calculation
        $vacationDaysPerYear = $user->vacation_days_per_year !== null
            ? (int)$user->vacation_days_per_year
            : ((int)\App\Models\Setting::where('key', 'vacation_days_per_year')->value('value') ?: 30);

        $yearlyVacationEntries = \App\Models\TimeEntry::whereHas('workDay', function ($query) use ($year, $user) {
            $query->whereYear('date', $year)->where('user_id', $user->id);
        })->whereHas('constructionSite', function ($query) {
            $query->where('name', 'Urlaub');
        })->sum('hours');

        $yearlyVacationDaysTaken = $defaultDailyHours > 0 ? $yearlyVacationEntries / $defaultDailyHours : 0;
        $remainingVacationDays = $vacationDaysPerYear - $yearlyVacationDaysTaken;

        $remark = \App\Models\MonthlyRemark::where('user_id', $user->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $appendPrevMonth = filter_var($request->input('append_prev_month', 'false'), FILTER_VALIDATE_BOOLEAN);
        $prevWorkDays = collect();

        if ($appendPrevMonth) {
            $prevMonthDate = $startOfMonth->copy()->subMonth();
            $prevWorkDays = $user->workDays()
                ->whereYear('date', $prevMonthDate->year)
                ->whereMonth('date', $prevMonthDate->month)
                ->with(['timeEntries.constructionSite'])
                ->orderBy('date')
                ->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly', compact('user', 'workDays', 'startOfMonth', 'year', 'month', 'previousMonthBalance', 'includeCarryover', 'vacationDaysPerYear', 'yearlyVacationDaysTaken', 'remainingVacationDays', 'remark', 'appendPrevMonth', 'prevWorkDays'));

        return $pdf->download("Monatsbericht_{$user->name}_{$month}_{$year}.pdf");
    }

    public function sendEmail(Request $request)
    {
        $year = (int)$request->input('year', date('Y'));
        $month = (int)$request->input('month', date('n'));
        $user = auth()->user();

        // Check if boss email is configured
        $bossEmail = \App\Models\Setting::where('key', 'boss_email')->value('value');
        if (!$bossEmail) {
            return back()->with('error', 'Keine E-Mail Adresse für den Chef hinterlegt. Bitte den Administrator kontaktieren.');
        }

        $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1);
        $monthName = $startOfMonth->locale('de')->isoFormat('MMMM');

        $workDays = $user->workDays()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->with(['timeEntries.constructionSite'])
            ->orderBy('date')
            ->get();

        // Load configured defaults once per request
        $defaultStart = \App\Models\Setting::where('key', 'default_start_time')->value('value') ?: '08:00';
        $defaultEnd = \App\Models\Setting::where('key', 'default_end_time')->value('value') ?: '16:00';
        $defaultBreak = \App\Models\Setting::where('key', 'default_break_duration')->value('value') !== null
            ? (int)\App\Models\Setting::where('key', 'default_break_duration')->value('value')
            : 0;

        $startTimeParse = \Carbon\Carbon::parse($defaultStart);
        $endTimeParse = \Carbon\Carbon::parse($defaultEnd);
        $diffMinutes = $startTimeParse->diffInMinutes($endTimeParse);
        $workMinutes = $diffMinutes; // Ignore break duration for target calculations
        $defaultDailyHours = round($workMinutes / 60, 2);

        // Check if carryover should be included (default: false if unchecked, true if checked - assuming checkbox sends 1)
        // Checkboxes only send value if checked. We set value="1".
        // If not present, input() returns null. We want default true? 
        // In the view I put `checked`, so it sends '1'. If user unchecks, it sends nothing.
        // So default should be false if key is missing? Or I use `has`.
        // Actually for checkboxes: if unchecked, strictly nothing is sent.
        // So $request->input('include_carryover', 0) would work if I rely on the fact that if it's sending, it's '1'.
        // But wait, the user request "mit oder ohne". 
        // Let's use boolean validation.
        $includeCarryover = $request->boolean('include_carryover');
        // Note: boolean() returns true for "1", "true", "on", "yes". False otherwise.
        // If unchecked, it's missing, so boolean() returns false? Laravel docs say 'missing' is false.
        // But I want it checked by default in UI. If user unchecks, sending nothing -> false. Correct.

        $prevActualHours = 0;
        $prevTargetHours = 0;
        $previousMonthBalance = 0;

        if ($includeCarryover) {
            $previousMonthDate = $startOfMonth->copy()->subMonth();
            $prevYear = $previousMonthDate->year;
            $prevMonth = $previousMonthDate->month;

            // Calculate Previous Month Balance
            // 1. Get Actual Hours
            $prevMonthWorkDays = $user->workDays()
                ->whereYear('date', $prevYear)
                ->whereMonth('date', $prevMonth)
                ->with('timeEntries')
                ->get();

            $prevActualHours = $prevMonthWorkDays->sum(function ($day) {
                return $day->timeEntries->sum('hours');
            });

            // 2. Calculate Target Hours (8h / weekday, dynamic adjustment)
            $daysInPrevMonth = $previousMonthDate->daysInMonth;
            for ($d = 1; $d <= $daysInPrevMonth; $d++) {
                $date = \Carbon\Carbon::createFromDate($prevYear, $prevMonth, $d);
                $dateString = $date->format('Y-m-d');

                $dayEntry = $prevMonthWorkDays->first(function ($day) use ($dateString) {
                    return \Carbon\Carbon::parse($day->date)->format('Y-m-d') === $dateString;
                });

                if (!$date->isWeekend()) {
                    $prevTargetHours += $defaultDailyHours;
                }
            }

            $previousMonthBalance = $prevActualHours - $prevTargetHours;
        }

        // Vacation Calculation
        $vacationDaysPerYear = $user->vacation_days_per_year !== null
            ? (int)$user->vacation_days_per_year
            : ((int)\App\Models\Setting::where('key', 'vacation_days_per_year')->value('value') ?: 30);

        // Calculate taken vacation in current year up to this month
        // We look at the whole year because vacation quota is per year
        $yearlyVacationEntries = \App\Models\TimeEntry::whereHas('workDay', function ($query) use ($year, $user) {
            $query->whereYear('date', $year)->where('user_id', $user->id);
        })->whereHas('constructionSite', function ($query) {
            $query->where('name', 'Urlaub');
        })->sum('hours');

        $yearlyVacationDaysTaken = $defaultDailyHours > 0 ? $yearlyVacationEntries / $defaultDailyHours : 0;
        $remainingVacationDays = $vacationDaysPerYear - $yearlyVacationDaysTaken;

        $remark = \App\Models\MonthlyRemark::where('user_id', $user->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $appendPrevMonth = $request->boolean('append_prev_month');
        $prevWorkDays = collect();

        if ($appendPrevMonth) {
            $prevMonthDate = $startOfMonth->copy()->subMonth();
            $prevWorkDays = $user->workDays()
                ->whereYear('date', $prevMonthDate->year)
                ->whereMonth('date', $prevMonthDate->month)
                ->with(['timeEntries.constructionSite'])
                ->orderBy('date')
                ->get();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly', compact('user', 'workDays', 'startOfMonth', 'year', 'month', 'previousMonthBalance', 'includeCarryover', 'vacationDaysPerYear', 'yearlyVacationDaysTaken', 'remainingVacationDays', 'remark', 'appendPrevMonth', 'prevWorkDays'));
        $pdfContent = $pdf->output();

        try {
            \Illuminate\Support\Facades\Mail::to($bossEmail)->send(new \App\Mail\MonthlyReportMail($user, $monthName, $year, $pdfContent));
            return back()->with('success', "Monatsbericht erfolgreich an $bossEmail gesendet.");
        }
        catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Senden der E-Mail: ' . $e->getMessage());
        }
    }
}
