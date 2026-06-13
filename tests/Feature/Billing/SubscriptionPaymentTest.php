<?php

use App\Enums\PaymentChannel;
use App\Enums\PaymentRequestStatus;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * A center admin without an active subscription (so billing is reachable).
 *
 * @return array{0: Client, 1: User, 2: Plan}
 */
function billingContext(): array
{
    $client = Client::factory()->create();
    $admin = User::factory()->clientAdmin($client)->create();
    $plan = Plan::factory()->create(['price' => 499]);

    return [$client, $admin, $plan];
}

it('lets a center admin submit a payment with a receipt', function () {
    Storage::fake('public');
    [$client, $admin, $plan] = billingContext();

    $this->actingAs($admin)->post(route('tenant.billing.store'), [
        'plan_id' => $plan->id,
        'amount' => 499,
        'channel' => PaymentChannel::InstaPay->value,
        'reference' => 'TXN-12345',
        'receipt' => UploadedFile::fake()->image('receipt.png'),
    ])->assertRedirect(route('tenant.billing.index'));

    $payment = SubscriptionPayment::withoutGlobalScopes()->first();
    expect($payment->client_id)->toBe($client->id)
        ->and($payment->status)->toBe(PaymentRequestStatus::Pending)
        ->and($payment->receipt_path)->not->toBeNull();
    Storage::disk('public')->assertExists($payment->receipt_path);
});

it('billing is reachable while the subscription is inactive', function () {
    [, $admin] = billingContext();

    $this->actingAs($admin)->get(route('tenant.billing.index'))->assertOk();
});

it('forbids a staff member from billing', function () {
    [$client] = billingContext();
    $staff = User::factory()->clientUser($client)->create();

    $this->actingAs($staff)->get(route('tenant.billing.index'))->assertForbidden();
});

it('activates the subscription when a super admin approves a payment', function () {
    [$client, , $plan] = billingContext();
    $payment = SubscriptionPayment::factory()->create([
        'client_id' => $client->id,
        'plan_id' => $plan->id,
        'amount' => 499,
    ]);
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)->post(route('admin.subscription-payments.approve', $payment))->assertRedirect();

    expect($payment->fresh()->status)->toBe(PaymentRequestStatus::Approved)
        ->and($payment->fresh()->reviewed_by)->toBe($superAdmin->id)
        ->and($client->fresh()->hasActiveSubscription())->toBeTrue();

    $subscription = Subscription::where('client_id', $client->id)->where('status', 'active')->first();
    expect((float) $subscription->price)->toBe(499.0);
});

it('rejects a payment without activating a subscription', function () {
    [$client, , $plan] = billingContext();
    $payment = SubscriptionPayment::factory()->create(['client_id' => $client->id, 'plan_id' => $plan->id]);
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)->post(route('admin.subscription-payments.reject', $payment), ['review_note' => 'Wrong amount'])
        ->assertRedirect();

    expect($payment->fresh()->status)->toBe(PaymentRequestStatus::Rejected)
        ->and($client->fresh()->hasActiveSubscription())->toBeFalse();
});

it('a center only sees its own payment requests', function () {
    [$client, $admin, $plan] = billingContext();
    SubscriptionPayment::factory()->create(['client_id' => $client->id, 'plan_id' => $plan->id, 'reference' => 'MINE-REF']);
    SubscriptionPayment::factory()->create(['reference' => 'FOREIGN-REF']);

    $this->actingAs($admin)->get(route('tenant.billing.index'))
        ->assertOk()
        ->assertSee('MINE-REF')
        ->assertDontSee('FOREIGN-REF');
});

it('forbids tenant users from the admin review queue', function () {
    [, $admin] = billingContext();

    $this->actingAs($admin)->get(route('admin.subscription-payments.index'))->assertForbidden();
});
