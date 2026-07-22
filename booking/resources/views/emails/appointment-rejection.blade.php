<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>{{ $appointment->confirmed_at ? 'Terminstornierung' : 'Ihre Terminanfrage' }}</title>
</head>
<body>
    <p>Hallo {{ $appointment->customer_name }},</p>

    @if ($appointment->confirmed_at)
        <p>leider müssen wir Ihnen mitteilen, dass Ihr bestätigter Termin am {{ $appointment->starts_at->format('d.m.Y') }} um {{ $appointment->starts_at->format('H:i') }} Uhr ({{ $appointment->service->name }}) storniert wurde.</p>
    @else
        <p>vielen Dank für Ihre Terminanfrage. Leider können wir Ihnen den gewünschten Termin am {{ $appointment->starts_at->format('d.m.Y') }} um {{ $appointment->starts_at->format('H:i') }} Uhr nicht bestätigen.</p>
    @endif

    <p><strong>Begründung:</strong></p>
    <p>{{ $appointment->admin_notes }}</p>

    <p>Wir bedauern die Unannehmlichkeiten und hoffen auf Ihr Verständnis.</p>

    <p>Mit freundlichen Grüßen<br>Ihr Team</p>
</body>
</html>
