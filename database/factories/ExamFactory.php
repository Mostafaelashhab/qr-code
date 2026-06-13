<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Exam;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'group_id' => fn (array $attributes) => Group::factory()->create(['client_id' => $attributes['client_id']])->id,
            'name' => fake()->randomElement(['Monthly Test', 'Midterm', 'Final', 'Quiz']),
            'exam_date' => now()->subDays(fake()->numberBetween(1, 20))->format('Y-m-d'),
            'max_score' => 100,
        ];
    }
}
