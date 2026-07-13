<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
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
            ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Confirmed])
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

    public function isSlotAvailable(Service $service, Carbon $startsAt): bool
    {
        return $this->getAvailableSlots($service, $startsAt->copy()->startOfDay())
            ->contains(fn (Carbon $slot) => $slot->format('Y-m-d H:i') === $startsAt->format('Y-m-d H:i'));
    }

    public function isDateBlocked(Carbon $date): bool
    {
        return BlockedDate::query()
            ->whereDate('date', $date->toDateString())
            ->exists();
    }

    /**
     * Verfügbarkeit pro Tag für einen Monat (Kalenderansicht).
     *
     * @return array<string, array{available: bool, slots_count: int}>
     */
    public function getMonthAvailability(Service $service, int $year, int $month): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $availability = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->lt(today())) {
                $availability[$date->format('Y-m-d')] = [
                    'available' => false,
                    'slots_count' => 0,
                ];

                continue;
            }

            $slots = $this->getAvailableSlots($service, $date);

            $availability[$date->format('Y-m-d')] = [
                'available' => $slots->isNotEmpty(),
                'slots_count' => $slots->count(),
            ];
        }

        return $availability;
    }
}
