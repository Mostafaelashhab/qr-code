<?php

namespace Database\Factories;

use App\Enums\ExpenseCategory;
use App\Models\Client;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => fake()->randomElement(['Rent', 'Electricity', 'Internet', 'Printing', 'Salaries']),
            'category' => fake()->randomElement(ExpenseCategory::cases()),
            'amount' => fake()->randomElement([500, 1000, 1500, 2500]),
            'spent_at' => now()->format('Y-m-d'),
            'note' => null,
        ];
    }
}
