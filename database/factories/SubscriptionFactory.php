<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = now()->subDays(fake()->numberBetween(1, 30));

        return [
            'client_id' => Client::factory(),
            'plan_id' => Plan::factory(),
            'status' => SubscriptionStatus::Active,
            'price' => fake()->randomElement([199, 499, 999]),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addMonth(),
            'notes' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => SubscriptionStatus::Active,
            'ends_at' => now()->addMonth(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => SubscriptionStatus::Expired,
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
        ]);
    }
}
