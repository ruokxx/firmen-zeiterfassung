<!DOCTYPE html>
<html>
<head>
    <title>Monatsbericht</title>
</head>
<body>
    <h1>Monatsbericht für {{ $monthName }} {{ $year }}</h1>
    <p>Hallo,</p>
    <p>anbei erhalten Sie den Monatsbericht von {{ $user->name }} für {{ $monthName }} {{ $year }}.</p>
    <p>Mit freundlichen Grüßen,<br>{{ config('app.name') }}</p>
</body>
</html>
