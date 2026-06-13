<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\Client;
use App\Models\Question;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'test_id' => fn (array $attributes) => Test::factory()->create(['client_id' => $attributes['client_id']])->id,
            'body' => fake()->sentence().'?',
            'type' => QuestionType::Mcq,
            'points' => 1,
            'sort_order' => 0,
        ];
    }
}
