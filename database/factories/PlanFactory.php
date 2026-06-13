<?php

namespace Database\Factories;

use App\Enums\BillingPeriod;
use App\Enums\Feature;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['Starter', 'Growth', 'Pro', 'Enterprise']);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->sentence(),
            'price' => fake()->randomElement([0, 199, 499, 999]),
            'billing_period' => fake()->randomElement(BillingPeriod::cases()),
            'max_users' => fake()->randomElement([5, 15, 50, null]),
            'features' => Feature::allValues(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Set the exact feature keys this plan grants.
     *
     * @param  array<int, Feature|string>  $features
     */
    public function features(array $features): static
    {
        return $this->state(fn (array $attributes): array => [
            'features' => array_map(fn ($f): string => $f instanceof Feature ? $f->value : $f, $features),
        ]);
    }
}
