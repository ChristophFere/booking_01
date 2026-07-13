<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Test-E-Mail</title>
</head>
<body>
    <h1>Test-E-Mail erfolgreich</h1>

    <p>Hallo {{ $adminName }},</p>

    <p>diese Test-E-Mail bestätigt, dass die E-Mail-Konfiguration in {{ config('app.name') }} funktioniert.</p>

    <p>Terminbestätigungen können mit diesen Einstellungen versendet werden.</p>
</body>
</html>
