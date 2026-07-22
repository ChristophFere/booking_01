<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentRejection extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->appointment->confirmed_at
            ? 'Terminstornierung'
            : 'Ihre Terminanfrage';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-rejection',
        );
    }
}
