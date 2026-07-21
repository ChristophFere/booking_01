<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Ihre Terminanfrage</title>
</head>
<body>
    <p>Hallo {{ $appointment->customer_name }},</p>

    <p>vielen Dank für Ihre Terminanfrage. Leider können wir Ihnen den gewünschten Termin am {{ $appointment->starts_at->format('d.m.Y') }} um {{ $appointment->starts_at->format('H:i') }} Uhr nicht bestätigen.</p>

    <p><strong>Begründung:</strong></p>
    <p>{{ $appointment->admin_notes }}</p>

    <p>Wir bedauern, Ihnen keine positive Rückmeldung geben zu können, und hoffen auf Ihr Verständnis.</p>

    <p>Mit freundlichen Grüßen<br>Ihr Team</p>
</body>
</html>
