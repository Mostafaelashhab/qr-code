<?php

namespace Database\Factories;

use App\Enums\SmsType;
use App\Models\Client;
use App\Models\SmsMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SmsMessage>
 */
class SmsMessageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'student_id' => null,
            'to' => fake()->phoneNumber(),
            'type' => fake()->randomElement(SmsType::cases()),
            'body' => fake()->sentence(),
            'status' => 'sent',
            'sent_at' => now(),
        ];
    }
}
