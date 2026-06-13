<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Client;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'student_id' => fn (array $attributes) => Student::factory()->create(['client_id' => $attributes['client_id']])->id,
            'group_id' => null,
            'amount' => fake()->randomElement([150, 200, 300]),
            'method' => fake()->randomElement(PaymentMethod::cases()),
            'for_month' => now()->format('Y-m'),
            'paid_at' => now()->format('Y-m-d'),
            'note' => null,
        ];
    }
}
