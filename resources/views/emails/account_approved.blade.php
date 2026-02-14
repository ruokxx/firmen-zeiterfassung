<!DOCTYPE html>
<html>
<head>
    <title>Account freigeschaltet</title>
</head>
<body>
    <h1>Hallo {{ $user->name }},</h1>
    <p>Dein Account wurde erfolgreich freigeschaltet.</p>
    <p>Du kannst dich nun einloggen und deine Arbeitszeiten erfassen.</p>
    <p>
        <a href="{{ route('login') }}">Zum Login</a>
    </p>
    <p>Mit freundlichen Grüßen,<br>{{ config('app.name') }}</p>
</body>
</html>
