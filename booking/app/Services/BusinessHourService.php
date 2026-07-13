<?php

namespace App\Services;

use App\Models\BusinessHour;
use Illuminate\Support\Collection;

class BusinessHourService
{
    /**
     * @return Collection<int, array{day_of_week: int, is_active: bool, opens_at: string, closes_at: string}>
     */
    public function getWeeklySchedule(): Collection
    {
        $existing = BusinessHour::query()
            ->get()
            ->keyBy('day_of_week');

        return collect(range(0, 6))->map(function (int $day) use ($existing) {
            $record = $existing->get($day);

            return [
                'day_of_week' => $day,
                'is_active' => $record?->is_active ?? false,
                'opens_at' => $this->formatTime($record?->opens_at ?? '09:00'),
                'closes_at' => $this->formatTime($record?->closes_at ?? '17:00'),
            ];
        });
    }

    /**
     * @param  array<int, array{is_active: bool, opens_at?: string|null, closes_at?: string|null}>  $hours
     */
    public function syncWeeklySchedule(array $hours): void
    {
        foreach ($hours as $day => $data) {
            BusinessHour::query()->updateOrCreate(
                ['day_of_week' => (int) $day],
                [
                    'is_active' => (bool) ($data['is_active'] ?? false),
                    'opens_at' => ($data['is_active'] ?? false) ? ($data['opens_at'] ?? '09:00') : '09:00',
                    'closes_at' => ($data['is_active'] ?? false) ? ($data['closes_at'] ?? '17:00') : '17:00',
                ],
            );
        }
    }

    private function formatTime(?string $time): string
    {
        if (! $time) {
            return '09:00';
        }

        return substr($time, 0, 5);
    }
}
