<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Services\EmailTemplateSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
    ) {}

    public function envelope(): Envelope
    {
        $rendered = app(EmailTemplateSettingsService::class)->renderConfirmation($this->appointment);

        return new Envelope(
            subject: $rendered['subject'],
        );
    }

    public function content(): Content
    {
        $rendered = app(EmailTemplateSettingsService::class)->renderConfirmation($this->appointment);

        return new Content(
            view: 'emails.dynamic',
            with: [
                'title' => $rendered['subject'],
                'body' => $rendered['body'],
            ],
        );
    }
}
