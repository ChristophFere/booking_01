<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+30 days');
        $duration = fake()->randomElement([30, 45, 60]);

        return [
            'service_id' => Service::factory(),
            'user_id' => null,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->optional()->phoneNumber(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify("+{$duration} minutes"),
            'status' => AppointmentStatus::Pending,
            'notes' => fake()->optional()->sentence(),
            'admin_notes' => null,
            'confirmation_token' => Str::random(64),
            'confirmed_at' => null,
            'cancelled_at' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Confirmed,
            'confirmed_at' => now(),
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ]);
    }
}
