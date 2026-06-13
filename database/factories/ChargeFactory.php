<?php

namespace Database\Factories;

use App\Models\Charge;
use App\Models\Client;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Charge>
 */
class ChargeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'student_id' => fn (array $attributes) => Student::factory()->create(['client_id' => $attributes['client_id']])->id,
            'group_id' => null,
            'title' => fake()->randomElement(['Monthly fee', 'Books', 'Registration']),
            'amount' => fake()->randomElement([200, 300, 400]),
            'discount' => 0,
            'for_month' => now()->format('Y-m'),
        ];
    }
}
