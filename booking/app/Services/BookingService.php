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
    public function __construct(
        private BookingSettingsService $bookingSettings,
    ) {}
    /**
     * Tagesübersicht aller Slots inkl. Status für die Kalenderansicht.
     *
     * @return array{
     *     blocked: bool,
     *     blocked_reason: string|null,
     *     slots: array<int, array{value: string, label: string, status: string}>
     * }
     */
    public function getDaySlotsOverview(Service $service, Carbon $date): array
    {
        if (! $this->bookingSettings->isDateBookable($date)) {
            return [
                'blocked' => false,
                'blocked_reason' => null,
                'slots' => [],
            ];
        }

        $blockedDate = BlockedDate::query()
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($blockedDate) {
            return [
                'blocked' => true,
                'blocked_reason' => $blockedDate->reason ?: 'Dieser Tag ist gesperrt.',
                'slots' => [],
            ];
        }

        $businessHour = BusinessHour::query()
            ->active()
            ->where('day_of_week', $date->dayOfWeek)
            ->first();

        if (! $businessHour) {
            return [
                'blocked' => false,
                'blocked_reason' => null,
                'slots' => [],
            ];
        }

        $slotDuration = $service->duration_minutes;
        $opensAt = $date->copy()->setTimeFromTimeString($businessHour->opens_at);
        $closesAt = $date->copy()->setTimeFromTimeString($businessHour->closes_at);

        $appointments = Appointment::query()
            ->whereDate('starts_at', $date)
            ->whereIn('status', [AppointmentStatus::Pending, AppointmentStatus::Confirmed])
            ->get(['starts_at', 'ends_at', 'status']);

        $slots = [];
        $current = $opensAt->copy();

        while ($current->copy()->addMinutes($slotDuration)->lte($closesAt)) {
            if (! $current->isFuture()) {
                $current->addMinutes($slotDuration);

                continue;
            }

            $slotEnd = $current->copy()->addMinutes($slotDuration);
            $status = 'available';

            foreach ($appointments as $appointment) {
                if ($current->lt($appointment->ends_at) && $slotEnd->gt($appointment->starts_at)) {
                    $status = $appointment->status === AppointmentStatus::Pending ? 'pending' : 'booked';

                    break;
                }
            }

            $slots[] = [
                'value' => $current->format('Y-m-d H:i:s'),
                'label' => $current->format('H:i').' Uhr',
                'status' => $status,
            ];

            $current->addMinutes($slotDuration);
        }

        return [
            'blocked' => false,
            'blocked_reason' => null,
            'slots' => $slots,
        ];
    }

    /**
     * Verfügbare Zeitslots für einen Tag und Service ermitteln.
     *
     * @return Collection<int, Carbon>
     */
    public function getAvailableSlots(Service $service, Carbon $date): Collection
    {
        $overview = $this->getDaySlotsOverview($service, $date);

        if ($overview['blocked']) {
            return collect();
        }

        return collect($overview['slots'])
            ->filter(fn (array $slot) => $slot['status'] === 'available')
            ->map(fn (array $slot) => Carbon::parse($slot['value']));
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
     * @return array<string, array{
     *     blocked: bool,
     *     blocked_reason: string|null,
     *     available: bool,
     *     slots_count: int,
     *     pending_count: int,
     *     clickable: bool
     * }>
     */
    public function getMonthAvailability(Service $service, int $year, int $month): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();
        $availability = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');

            if ($date->lt(today()) || ! $this->bookingSettings->isDateBookable($date)) {
                $availability[$dateKey] = [
                    'blocked' => false,
                    'blocked_reason' => null,
                    'available' => false,
                    'slots_count' => 0,
                    'pending_count' => 0,
                    'clickable' => false,
                ];

                continue;
            }

            $overview = $this->getDaySlotsOverview($service, $date);
            $slots = collect($overview['slots']);
            $availableCount = $slots->where('status', 'available')->count();
            $pendingCount = $slots->where('status', 'pending')->count();

            $availability[$dateKey] = [
                'blocked' => $overview['blocked'],
                'blocked_reason' => $overview['blocked_reason'],
                'available' => $availableCount > 0,
                'slots_count' => $availableCount,
                'pending_count' => $pendingCount,
                'clickable' => ! $overview['blocked'] && $slots->isNotEmpty(),
            ];
        }

        return $availability;
    }
}
