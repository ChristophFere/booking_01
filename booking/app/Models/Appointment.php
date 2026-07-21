<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_id',
    'user_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'starts_at',
    'ends_at',
    'status',
    'notes',
    'admin_notes',
    'confirmation_token',
    'confirmed_at',
    'cancelled_at',
    'confirmation_mail_sent_at',
    'cancellation_mail_sent_at',
])]
class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => AppointmentStatus::class,
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'confirmation_mail_sent_at' => 'datetime',
            'cancellation_mail_sent_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === AppointmentStatus::Pending;
    }

    public function isConfirmed(): bool
    {
        return $this->status === AppointmentStatus::Confirmed;
    }

    public function isCancelled(): bool
    {
        return $this->status === AppointmentStatus::Cancelled;
    }

    public function hasConfirmationMailBeenSent(): bool
    {
        return $this->confirmation_mail_sent_at !== null;
    }

    public function hasCancellationMailBeenSent(): bool
    {
        return $this->cancellation_mail_sent_at !== null;
    }
}
