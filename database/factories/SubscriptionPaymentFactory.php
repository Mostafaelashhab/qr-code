<?php

namespace Database\Factories;

use App\Enums\PaymentChannel;
use App\Enums\PaymentRequestStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\SubscriptionPayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SubscriptionPayment>
 */
class SubscriptionPaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'plan_id' => Plan::factory(),
            'amount' => fake()->randomElement([199, 499, 999]),
            'channel' => fake()->randomElement(PaymentChannel::cases()),
            'reference' => fake()->bothify('TXN-#######'),
            'status' => PaymentRequestStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => ['status' => PaymentRequestStatus::Approved]);
    }
}
