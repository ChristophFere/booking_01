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
        $defaults = [
            0 => ['opens_at' => '09:00', 'closes_at' => '17:00', 'is_active' => false],
            1 => ['opens_at' => '09:00', 'closes_at' => '17:00', 'is_active' => true],
            2 => ['opens_at' => '09:00', 'closes_at' => '17:00', 'is_active' => true],
            3 => ['opens_at' => '09:00', 'closes_at' => '17:00', 'is_active' => true],
            4 => ['opens_at' => '09:00', 'closes_at' => '17:00', 'is_active' => true],
            5 => ['opens_at' => '09:00', 'closes_at' => '15:00', 'is_active' => true],
            6 => ['opens_at' => '09:00', 'closes_at' => '17:00', 'is_active' => false],
        ];

        foreach ($defaults as $day => $hours) {
            BusinessHour::query()->updateOrCreate(
                ['day_of_week' => $day],
                $hours,
            );
        }
    }
}
