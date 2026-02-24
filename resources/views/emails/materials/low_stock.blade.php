<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lagerbestand Warnung</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
        <h2 style="color: #d9534f;">Achtung: Geringer Lagerbestand!</h2>
        
        <p>Hallo,</p>
        
        <p>das Material <strong>{{ $material->name }}</strong> hat den Mindestbestand erreicht oder unterschritten.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Material:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $material->name }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Aktueller Bestand:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">
                    <span style="color: #d9534f; font-weight: bold;">{{ $material->stock_count }}</span>
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong>Warnschwelle:</strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">{{ $material->low_stock_threshold }}</td>
            </tr>
        </table>
        
        <p>Bitte prüfen Sie den Bestand und bestellen Sie bei Bedarf nach.</p>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #777;">
            Dies ist eine automatisch generierte E-Mail aus der Work-Time-Tracker Lagerverwaltung.
        </p>
    </div>
</body>
</html>
