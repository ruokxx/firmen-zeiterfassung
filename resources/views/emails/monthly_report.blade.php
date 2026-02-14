<!DOCTYPE html>
<html>
<head>
    <title>Monatsbericht</title>
</head>
<body>
    <div style="font-family: sans-serif; line-height: 1.5;">
        @if(isset($customBody) && !empty($customBody))
            {!! nl2br(e($customBody)) !!}
        @else
            <p>Hallo,</p>
            <p>anbei erhalten Sie den Monatsbericht von <strong>{{ $user->name ?? 'Mitarbeiter' }}</strong>.</p>
            <p>Mit freundlichen Grüßen,<br>{{ config('app.name') }}</p>
        @endif
    </div>
</body>
</html>
