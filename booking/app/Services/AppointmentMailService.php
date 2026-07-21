<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentRejection;
use App\Models\Appointment;
use Illuminate\Support\Facades\Mail;

class AppointmentMailService
{
    public function __construct(
        private MailSettingsService $mailSettings,
    ) {}

    public function sendConfirmationMail(Appointment $appointment): void
    {
        $this->mailSettings->applyToConfig();

        if ($appointment->hasConfirmationMailBeenSent()) {
            return;
        }

        if ($appointment->status !== AppointmentStatus::Confirmed) {
            return;
        }

        $appointment->loadMissing('service');

        Mail::to($appointment->customer_email)->send(new AppointmentConfirmation($appointment));

        $appointment->update(['confirmation_mail_sent_at' => now()]);
    }

    public function sendRejectionMail(Appointment $appointment): void
    {
        $this->mailSettings->applyToConfig();

        if ($appointment->hasCancellationMailBeenSent()) {
            return;
        }

        if ($appointment->status !== AppointmentStatus::Cancelled) {
            return;
        }

        $appointment->loadMissing('service');

        Mail::to($appointment->customer_email)->send(new AppointmentRejection($appointment));

        $appointment->update(['cancellation_mail_sent_at' => now()]);
    }
}
