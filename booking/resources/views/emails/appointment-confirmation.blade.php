<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Terminbestätigung</title>
</head>
<body>
    <p>Hallo {{ $appointment->customer_name }},</p>

    <p>wir freuen uns, Ihnen mitteilen zu können, dass Ihr Termin bestätigt wurde.</p>

    <p><strong>Ihre Termindetails:</strong></p>
    <ul>
        <li><strong>Leistung:</strong> {{ $appointment->service->name }}</li>
        <li><strong>Datum:</strong> {{ $appointment->starts_at->format('d.m.Y') }}</li>
        <li><strong>Uhrzeit:</strong> {{ $appointment->starts_at->format('H:i') }} – {{ $appointment->ends_at->format('H:i') }} Uhr</li>
    </ul>

    <p>Bitte erscheinen Sie pünktlich zu Ihrem Termin. Bei Fragen können Sie uns gerne kontaktieren.</p>

    <p>Mit freundlichen Grüßen<br>Ihr Team</p>
</body>
</html>
