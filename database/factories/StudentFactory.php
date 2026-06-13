<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'guardian_phone' => fake()->phoneNumber(),
            'stage' => fake()->randomElement(['Grade 10', 'Grade 11', 'Grade 12']),
            'is_active' => true,
        ];
    }
}
