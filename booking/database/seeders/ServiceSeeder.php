<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Erstberatung',
                'description' => 'Kostenlose Erstberatung zu Ihrem Anliegen (30 Minuten).',
                'duration_minutes' => 30,
                'price' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standardtermin',
                'description' => 'Regulärer Beratungstermin (60 Minuten).',
                'duration_minutes' => 60,
                'price' => 79.00,
                'sort_order' => 2,
            ],
            [
                'name' => 'Intensivtermin',
                'description' => 'Ausführliche Beratung für komplexe Anliegen (90 Minuten).',
                'duration_minutes' => 90,
                'price' => 119.00,
                'sort_order' => 3,
            ],
        ];

        foreach ($services as $service) {
            Service::query()->create([
                ...$service,
                'slug' => Str::slug($service['name']),
                'is_active' => true,
            ]);
        }
    }
}
