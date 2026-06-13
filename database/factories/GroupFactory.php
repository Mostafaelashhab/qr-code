<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Group;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'subject_id' => fn (array $attributes) => Subject::factory()->create(['client_id' => $attributes['client_id']])->id,
            'teacher_id' => null,
            'name' => 'Group '.fake()->randomLetter().fake()->numberBetween(1, 9),
            'monthly_fee' => fake()->randomElement([150, 200, 300]),
            'capacity' => fake()->randomElement([10, 20, null]),
            'schedule' => fake()->randomElement(['Sun/Tue 4pm', 'Mon/Wed 6pm', 'Sat 10am']),
            'is_active' => true,
        ];
    }
}
