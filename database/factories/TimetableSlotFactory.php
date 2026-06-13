<?php

namespace Database\Factories;

use App\Enums\Weekday;
use App\Models\Client;
use App\Models\Group;
use App\Models\TimetableSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimetableSlot>
 */
class TimetableSlotFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'group_id' => fn (array $attributes) => Group::factory()->create(['client_id' => $attributes['client_id']])->id,
            'weekday' => fake()->randomElement(Weekday::cases()),
            'start_time' => '16:00',
            'end_time' => '17:30',
            'room' => fake()->randomElement(['Room 1', 'Room 2', null]),
        ];
    }
}
