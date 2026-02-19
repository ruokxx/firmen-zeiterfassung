<!DOCTYPE html>
<html>
<head>
    <title>Arbeitszeit-Erinnerung</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333333;">Hallo {{ $user->name }},</h2>
        <p style="color: #555555; font-size: 16px;">
            Dies ist eine freundliche Erinnerung, dass du für den heutigen Tag ({{ \Carbon\Carbon::today()->locale('de')->isoFormat('dddd, D. MMMM YYYY') }}) noch keine 8 Stunden Arbeitszeit erfasst hast.
        </p>
        <p style="color: #555555; font-size: 16px;">
            Bitte logge dich ein und trage deine fehlenden Stunden nach.
        </p>
        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('dashboard') }}" style="background-color: #ea580c; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">Zum Dashboard</a>
        </div>
        <p style="color: #999999; font-size: 12px; margin-top: 40px; text-align: center;">
            Du erhältst diese E-Mail, weil du die tägliche Erinnerung in deinem Profil aktiviert hast.
            <br>
            <a href="{{ route('profile.edit') }}" style="color: #ea580c;">Einstellungen ändern</a>
        </p>
    </div>
</body>
</html>
