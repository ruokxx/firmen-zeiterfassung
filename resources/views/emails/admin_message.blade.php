<!DOCTYPE html>
<html>
<head>
    <title>Nachricht vom Administrator</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #1f2937; margin-top: 0;">Nachricht vom Administrator</h2>
        
        <div style="color: #4b5563; line-height: 1.5; margin-bottom: 20px; white-space: pre-line;">
            {{ $messageContent }}
        </div>

        <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">
        
        <p style="color: #6b7280; font-size: 12px; text-align: center;">
            Dies ist eine automatisch generierte Nachricht von {{ config('app.name') }}. Bitte antworten Sie nicht direkt auf diese E-Mail, es sei denn, eine Antwortadresse ist angegeben.
        </p>
    </div>
</body>
</html>
