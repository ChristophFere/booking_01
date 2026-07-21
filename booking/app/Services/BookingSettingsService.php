<?php

namespace App\Services;

use App\Models\Setting;
use Carbon\Carbon;

class BookingSettingsService
{
    private const KEY_AVAILABLE_FROM = 'booking.available_from';

    private const KEY_AVAILABLE_UNTIL = 'booking.available_until';

    public function getAvailableFrom(): Carbon
    {
        $stored = Setting::get(self::KEY_AVAILABLE_FROM);

        if ($stored) {
            return Carbon::parse($stored)->startOfDay();
        }

        return today();
    }

    public function getAvailableUntil(): Carbon
    {
        $stored = Setting::get(self::KEY_AVAILABLE_UNTIL);

        if ($stored) {
            return Carbon::parse($stored)->startOfDay();
        }

        return today()->addMonths(6);
    }

    public function isDateBookable(Carbon $date): bool
    {
        $date = $date->copy()->startOfDay();

        return $date->gte($this->getAvailableFrom())
            && $date->lte($this->getAvailableUntil());
    }

    /**
     * @return array{available_from: string, available_until: string}
     */
    public function all(): array
    {
        return [
            'available_from' => $this->getAvailableFrom()->format('Y-m-d'),
            'available_until' => $this->getAvailableUntil()->format('Y-m-d'),
        ];
    }

    /**
     * @param  array{available_from: string, available_until: string}  $data
     */
    public function save(array $data): void
    {
        Setting::set(self::KEY_AVAILABLE_FROM, $data['available_from']);
        Setting::set(self::KEY_AVAILABLE_UNTIL, $data['available_until']);
    }
}
