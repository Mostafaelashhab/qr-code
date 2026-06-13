<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Question;
use App\Models\TestAnswer;
use App\Models\TestAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TestAnswer>
 */
class TestAnswerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'test_attempt_id' => fn (array $attributes) => TestAttempt::factory()->create(['client_id' => $attributes['client_id']])->id,
            'question_id' => fn (array $attributes) => Question::factory()->create(['client_id' => $attributes['client_id']])->id,
            'question_option_id' => null,
            'is_correct' => false,
        ];
    }
}
