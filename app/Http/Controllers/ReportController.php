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
            ->get(); // Fixed missing semicolon here or after ->with if get was missing

        $targetHoursMonth = 0;
        $daysInMonth = $startOfMonth->daysInMonth;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = \Carbon\Carbon::createFromDate($year, $month, $d);
            $dateString = $date->format('Y-m-d');

            $dayEntry = $workDays->first(function ($day) use ($dateString) {
                return \Carbon\Carbon::parse($day->date)->format('Y-m-d') === $dateString;
            });

            if (!$date->isWeekend()) {
                $targetHoursMonth += 8;
            }
        }

        // This variable is needed for the footer calculation in blade if we want to be consistent,
        // but the blade currently only uses $previousMonthBalance. 
        // Checks show blade uses $totalHoursMonth + $previousMonthBalance. 
        // It doesn't display "Target Current" in footer, but for correctness of "Overtime" calculation if added later.

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
                $prevTargetHours += 8;
            }
        }

        $previousMonthBalance = $prevActualHours - $prevTargetHours;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly', compact('user', 'workDays', 'startOfMonth', 'year', 'month', 'previousMonthBalance'));

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
                $prevTargetHours += 8;
            }
        }

        $previousMonthBalance = $prevActualHours - $prevTargetHours;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly', compact('user', 'workDays', 'startOfMonth', 'year', 'month', 'previousMonthBalance'));
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
