<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Monatsbericht</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { margin-bottom: 30px; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header" style="position: relative;">
        <div style="position: absolute; right: 0; top: -20px;">
            @php
                $pdfLogo = \App\Models\Setting::where('key', 'pdf_logo')->value('value');
                $logoPath = $pdfLogo ? storage_path('app/public/' . $pdfLogo) : public_path('logo_pdf.jpg');
            @endphp
            <img src="{{ $logoPath }}" style="width: 150px; height: auto;" alt="Logo"/>
        </div>
        <h1>Monatsbericht</h1>
        <p><strong>Mitarbeiter:</strong> {{ $user->name }}</p>
        <p><strong>E-Mail:</strong> {{ $user->email }}</p>
        <p><strong>Adresse:</strong> {{ $user->address }}</p>
        <p><strong>Monat:</strong> {{ $startOfMonth->locale('de')->isoFormat('MMMM YYYY') }}</p>
        <div style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 5px;">
            <p><strong>Urlaubstage (Jahr):</strong> {{ $vacationDaysPerYear }} Tage</p>
            <p><strong>Genommen (Jahr):</strong> {{ number_format($yearlyVacationDaysTaken, 1) }} Tage</p>
            <p><strong>Verbleibend:</strong> <span style="{{ $remainingVacationDays < 0 ? 'color: red;' : '' }}">{{ number_format($remainingVacationDays, 1) }} Tage</span></p>
        </div>
        <p><strong>Erstellt am:</strong> {{ date('d.m.Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Start/Ende</th>
                <th>Pause</th>
                <th>Gesamt</th>
                <th>Details (Baustellen)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalHoursMonth = 0;
            @endphp
            @foreach($workDays as $day)
                @php
                    // Calculate duration based on model logic
                    $hours = $day->total_hours;
                    $totalHoursMonth += $hours;
                @endphp
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}<br>
                        <span style="font-size: 10px; color: #666;">{{ \Carbon\Carbon::parse($day->date)->locale('de')->isoFormat('dddd') }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($day->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($day->end_time)->format('H:i') }}</td>
                    <td>{{ $day->break_duration }} Min</td>
                    <td>{{ number_format($hours, 1) }} h</td>
                    <td>
                        @foreach($day->timeEntries as $entry)
                            <div>{{ $entry->constructionSite->name }}: {{ number_format($entry->hours, 1) }} h</div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Gesamtstunden:</td>
                <td colspan="2" style="font-weight: bold;">{{ number_format($totalHoursMonth, 1) }} h</td>
            </tr>
        </tfoot>
    </table>

    @if(isset($remark) && $remark->remark)
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <h3>Sonstiges:</h3>
        <p style="white-space: pre-wrap; font-family: sans-serif; font-size: 11px; color: #333;">{{ $remark->remark }}</p>
    </div>
    @endif

    @if(isset($appendPrevMonth) && $appendPrevMonth && isset($prevWorkDays) && $prevWorkDays->count() > 0)
    <div style="page-break-before: always;"></div>
    <h2>Anhang: Vormonat ({{ $startOfMonth->copy()->subMonth()->locale('de')->isoFormat('MMMM YYYY') }})</h2>
    <table>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Start/Ende</th>
                <th>Pause</th>
                <th>Gesamt</th>
                <th>Details (Baustellen)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPrevHoursMonth = 0;
            @endphp
            @foreach($prevWorkDays as $day)
                @php
                    $hours = $day->total_hours;
                    $totalPrevHoursMonth += $hours;
                @endphp
                <tr>
                    <td>
                        {{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}<br>
                        <span style="font-size: 10px; color: #666;">{{ \Carbon\Carbon::parse($day->date)->locale('de')->isoFormat('dddd') }}</span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($day->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($day->end_time)->format('H:i') }}</td>
                    <td>{{ $day->break_duration }} Min</td>
                    <td>{{ number_format($hours, 1) }} h</td>
                    <td>
                        @foreach($day->timeEntries as $entry)
                            <div>{{ $entry->constructionSite->name }}: {{ number_format($entry->hours, 1) }} h</div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Gesamtstunden (Vormonat):</td>
                <td colspan="2" style="font-weight: bold;">{{ number_format($totalPrevHoursMonth, 1) }} h</td>
            </tr>
        </tfoot>
    </table>
    @endif

    <div class="footer">
        Seite 1 von 1
    </div>
</body>
</html>
