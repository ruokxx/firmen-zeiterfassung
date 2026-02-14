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
    <div class="header">
        <h1>Monatsbericht</h1>
        <p><strong>Mitarbeiter:</strong> {{ $user->name }}</p>
        <p><strong>Monat:</strong> {{ $startOfMonth->locale('de')->isoFormat('MMMM YYYY') }}</p>
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
                    $startTime = \Carbon\Carbon::parse($day->start_time);
                    $endTime = \Carbon\Carbon::parse($day->end_time);
                    // Calculate duration in hours minus break
                    $duration = $endTime->diffInMinutes($startTime) - $day->break_duration;
                    $hours = $duration / 60;
                    $totalHoursMonth += $hours;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
                    <td>{{ $startTime->format('H:i') }} - {{ $endTime->format('H:i') }}</td>
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

    <div class="footer">
        Seite 1 von 1
    </div>
</body>
</html>
