<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neuer Benutzer registriert</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>Ein neuer Benutzer hat sich registriert</h2>
    
    <p>Hallo Admin,</p>

    <p>ein neuer Benutzer hat sich auf der Arbeitszeiterfassungs-Plattform registriert und wartet auf Freischaltung.</p>

    <ul>
        <li><strong>Name:</strong> {{ $user->name }}</li>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Registriert am:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</li>
    </ul>

    <p>Bitte logge dich ein, um den Account zu überprüfen und freizuschalten.</p>

    <p>
        <a href="{{ route('admin.dashboard') }}" style="background-color: #f97316; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Zum Admin-Dashboard</a>
    </p>

    <p>Mit freundlichen Grüßen,<br>Dein System</p>
</body>
</html>
