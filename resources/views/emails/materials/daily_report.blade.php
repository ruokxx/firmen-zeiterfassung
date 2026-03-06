<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Täglicher Materialbericht</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background-color: #f9fafb; font-weight: bold; color: #4b5563; }
        .total-box { background-color: #fff7ed; border-left: 4px solid #f97316; padding: 15px; margin-bottom: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #6b7280; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; color: #111827;">Täglicher Materialbericht</h2>
            @if(isset($customBody) && !empty($customBody))
                <p style="margin: 5px 0 0 0; color: #6b7280; white-space: pre-wrap;">{{ $customBody }}</p>
            @else
                <p style="margin: 5px 0 0 0; color: #6b7280;">Übersicht der heutigen Entnahmen am {{ $date }}</p>
            @endif
        </div>

        @if($transactions->isEmpty())
            <div class="total-box text-center">
                <p style="margin: 0; font-weight: bold; color: #c2410c;">Heute wurden keine Materialien aus dem Lager entnommen.</p>
            </div>
        @else
            <div class="total-box">
                <p style="margin: 0; font-weight: bold; color: #c2410c;">
                    Insgesamt gab es heute {{ $transactions->count() }} Entnahme-Vorgänge aus dem Lager.
                </p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Uhrzeit</th>
                        <th>Mitarbeiter</th>
                        <th>Material</th>
                        <th>Menge</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td style="color: #6b7280; font-size: 14px;">{{ $transaction->created_at->format('H:i') }}</td>
                            <td>{{ $transaction->user ? $transaction->user->name : 'Gelöschter Nutzer' }}</td>
                            <td style="font-weight: bold;">{{ $transaction->material ? $transaction->material->name : 'Gelöschtes Material' }}</td>
                            <td style="color: #ea580c; font-weight: bold;">-{{ $transaction->quantity }} {{ $transaction->material ? $transaction->material->unit : 'Stück' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <p>Diese E-Mail wurde automatisch vom Work-Time-Tracker System generiert.</p>
            <p>Du kannst diese Berichte jederzeit in der Materialverwaltung unter "Einstellungen" (Lager) deaktivieren.</p>
        </div>
    </div>
</body>
</html>
