<?php

use App\Enums\ExpenseCategory;
use App\Models\Expense;

it('records an expense for the tenant', function () {
    [$client, $admin] = tenantWithAdmin();

    $this->actingAs($admin)->post(route('tenant.expenses.store'), [
        'title' => 'Office rent',
        'category' => ExpenseCategory::Rent->value,
        'amount' => 2000,
        'spent_at' => now()->toDateString(),
    ])->assertRedirect(route('tenant.expenses.index'));

    $expense = Expense::withoutGlobalScopes()->first();
    expect($expense->client_id)->toBe($client->id)
        ->and($expense->category)->toBe(ExpenseCategory::Rent);
});

it('only lists the tenant own expenses', function () {
    [$client, $admin] = tenantWithAdmin();
    Expense::factory()->create(['client_id' => $client->id, 'title' => 'Mine Expense']);
    Expense::factory()->create(['title' => 'Foreign Expense']);

    $this->actingAs($admin)->get(route('tenant.expenses.index'))
        ->assertOk()
        ->assertSee('Mine Expense')
        ->assertDontSee('Foreign Expense');
});
