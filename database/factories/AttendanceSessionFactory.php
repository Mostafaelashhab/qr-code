<?php

namespace Database\Factories;

use App\Models\AttendanceSession;
use App\Models\Client;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceSession>
 */
class AttendanceSessionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'group_id' => fn (array $attributes) => Group::factory()->create(['client_id' => $attributes['client_id']])->id,
            'session_date' => fake()->dateTimeBetween('-1 month')->format('Y-m-d'),
            'note' => null,
        ];
    }
}
