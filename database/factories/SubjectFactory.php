<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->randomElement(['Math', 'Physics', 'Chemistry', 'Arabic', 'English']),
            'stage' => fake()->randomElement(['Grade 10', 'Grade 11', 'Grade 12']),
            'is_active' => true,
        ];
    }
}
