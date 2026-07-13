<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BlockedDate;
use App\Models\BusinessHour;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingService
{
    /**
     * Verfügbare Zeitslots für einen Tag und Service ermitteln.
     *
     * @return Collection<int, Carbon>
     */
    public function getAvailableSlots(Service $service, Carbon $date): Collection
    {
        if ($this->isDateBlocked($date)) {
            return collect();
        }

        $businessHour = BusinessHour::query()
            ->active()
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (! $businessHour) {
            return collect();
        }

        $slotDuration = $service->duration_minutes;
        $opensAt = $date->copy()->setTimeFromTimeString($businessHour->opens_at);
        $closesAt = $date->copy()->setTimeFromTimeString($businessHour->closes_at);

        $bookedAppointments = Appointment::query()
            ->whereDate('starts_at', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['starts_at', 'ends_at']);

        $slots = collect();
        $current = $opensAt->copy();

        while ($current->copy()->addMinutes($slotDuration)->lte($closesAt)) {
            $slotEnd = $current->copy()->addMinutes($slotDuration);

            $isBooked = $bookedAppointments->contains(function (Appointment $appointment) use ($current, $slotEnd) {
                return $current->lt($appointment->ends_at) && $slotEnd->gt($appointment->starts_at);
            });

            if (! $isBooked && $current->isFuture()) {
                $slots->push($current->copy());
            }

            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    public function isDateBlocked(Carbon $date): bool
    {
        return BlockedDate::query()
            ->whereDate('date', $date->toDateString())
            ->exists();
    }
}
