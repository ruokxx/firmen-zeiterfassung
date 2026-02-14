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
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly', compact('user', 'workDays', 'startOfMonth', 'year', 'month'));

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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.monthly', compact('user', 'workDays', 'startOfMonth', 'year', 'month'));
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
