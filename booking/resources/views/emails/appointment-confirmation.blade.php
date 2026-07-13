<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Terminbestätigung</title>
</head>
<body>
    <h1>Ihre Terminbuchung</h1>

    <p>Hallo {{ $appointment->customer_name }},</p>

    <p>vielen Dank für Ihre Buchung. Hier sind Ihre Termindetails:</p>

    <ul>
        <li><strong>Leistung:</strong> {{ $appointment->service->name }}</li>
        <li><strong>Datum:</strong> {{ $appointment->starts_at->format('d.m.Y') }}</li>
        <li><strong>Uhrzeit:</strong> {{ $appointment->starts_at->format('H:i') }} – {{ $appointment->ends_at->format('H:i') }} Uhr</li>
        <li><strong>Status:</strong> {{ $appointment->status->label() }}</li>
    </ul>

    @if ($appointment->notes)
        <p><strong>Ihre Notiz:</strong> {{ $appointment->notes }}</p>
    @endif

    <p>Bei Fragen antworten Sie einfach auf diese E-Mail.</p>
</body>
</html>
