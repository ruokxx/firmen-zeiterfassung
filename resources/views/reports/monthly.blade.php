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
            <img src="{{ public_path('logo_pdf.jpg') }}" style="width: 150px; height: auto;" alt="Logo"/>
        </div>
        <h1>Monatsbericht</h1>
        <p><strong>Mitarbeiter:</strong> {{ $user->name }}</p>
        <p><strong>E-Mail:</strong> {{ $user->email }}</p>
        <p><strong>Adresse:</strong> {{ $user->address }}</p>
        <p><strong>Monat:</strong> {{ $startOfMonth->locale('de')->isoFormat('MMMM YYYY') }}</p>
        @if($includeCarryover)
        <p><strong>Übertrag aus Vormonat:</strong> 
            <span style="{{ $previousMonthBalance < 0 ? 'color: red;' : ($previousMonthBalance > 0 ? 'color: green;' : '') }}">
                {{ number_format($previousMonthBalance, 1) }} h
            </span>
        </p>
        @endif
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
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
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
                <td colspan="3" style="text-align: right; font-weight: bold;">Gesamtstunden (Aktuell):</td>
                <td colspan="2" style="font-weight: bold;">{{ number_format($totalHoursMonth, 1) }} h</td>
            </tr>
            @if($includeCarryover)
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Übertrag Vormonat:</td>
                <td colspan="2" style="font-weight: bold; {{ $previousMonthBalance < 0 ? 'color: red;' : ($previousMonthBalance > 0 ? 'color: green;' : '') }}">
                    {{ number_format($previousMonthBalance, 1) }} h
                </td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td colspan="3" style="text-align: right; font-weight: bold;">Gesamt (inkl. Übertrag):</td>
                <td colspan="2" style="font-weight: bold;">
                    {{ number_format($totalHoursMonth + $previousMonthBalance, 1) }} h
                </td>
            </tr>
            @endif
        </tfoot>
    </table>

    @if(isset($remark) && $remark->remark)
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <h3>Sonstiges:</h3>
        <p style="white-space: pre-wrap; font-family: sans-serif; font-size: 11px; color: #333;">{{ $remark->remark }}</p>
    </div>
    @endif

    <div class="footer">
        Seite 1 von 1
    </div>
</body>
</html>
