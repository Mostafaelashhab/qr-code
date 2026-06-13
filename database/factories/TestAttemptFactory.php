<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Student;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestAttempt>
 */
class TestAttemptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'test_id' => fn (array $attributes) => Test::factory()->create(['client_id' => $attributes['client_id']])->id,
            'student_id' => fn (array $attributes) => Student::factory()->create(['client_id' => $attributes['client_id']])->id,
            'started_at' => now(),
        ];
    }
}
