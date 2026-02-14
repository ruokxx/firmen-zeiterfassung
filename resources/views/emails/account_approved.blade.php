<!DOCTYPE html>
<html>
<head>
    <title>Account freigeschaltet</title>
</head>
<body>
    <div style="font-family: sans-serif; line-height: 1.5;">
        @if(isset($customBody))
            {!! nl2br(e($customBody)) !!}
        @else
            <p>Hallo,</p>
            <p>Dein Account wurde erfolgreich freigeschaltet.</p>
            <p>Du kannst dich nun einloggen.</p>
            <p>Mit freundlichen Grüßen,<br>{{ config('app.name') }}</p>
        @endif
    </div>
</body>
</html>
