<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Group;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Test>
 */
class TestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'group_id' => fn (array $attributes) => Group::factory()->create(['client_id' => $attributes['client_id']])->id,
            'title' => fake()->randomElement(['Quiz 1', 'Midterm', 'Unit Test']),
            'duration_minutes' => 30,
            'shuffle' => true,
            'show_results' => true,
            'is_published' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => ['is_published' => false]);
    }
}
