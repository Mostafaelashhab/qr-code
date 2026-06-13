<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'exam_id' => fn (array $attributes) => Exam::factory()->create(['client_id' => $attributes['client_id']])->id,
            'student_id' => fn (array $attributes) => Student::factory()->create(['client_id' => $attributes['client_id']])->id,
            'score' => fake()->numberBetween(40, 100),
        ];
    }
}
