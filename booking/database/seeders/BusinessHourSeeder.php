<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use Illuminate\Database\Seeder;

class BusinessHourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hours = [
            ['day_of_week' => 1, 'opens_at' => '09:00', 'closes_at' => '17:00'],
            ['day_of_week' => 2, 'opens_at' => '09:00', 'closes_at' => '17:00'],
            ['day_of_week' => 3, 'opens_at' => '09:00', 'closes_at' => '17:00'],
            ['day_of_week' => 4, 'opens_at' => '09:00', 'closes_at' => '17:00'],
            ['day_of_week' => 5, 'opens_at' => '09:00', 'closes_at' => '15:00'],
        ];

        foreach ($hours as $hour) {
            BusinessHour::query()->create([
                ...$hour,
                'is_active' => true,
            ]);
        }
    }
}
