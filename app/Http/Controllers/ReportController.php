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
        $workMinutes = $diffMinutes - $defaultBreak; // Subtract break duration
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

        $includeCarryover = false;
        $previousMonthBalance = 0;



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
        $workMinutes = $diffMinutes - $defaultBreak; // Subtract break duration
        $defaultDailyHours = round($workMinutes / 60, 2);
        $includeCarryover = false;
        $previousMonthBalance = 0;

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
