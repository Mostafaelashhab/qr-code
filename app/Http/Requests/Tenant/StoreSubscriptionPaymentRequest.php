<?php

namespace App\Http\Requests\Tenant;

use App\Enums\PaymentChannel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreSubscriptionPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isClientAdmin();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('is_active', true)],
            'amount' => ['required', 'numeric', 'min:1'],
            'channel' => ['required', new Enum(PaymentChannel::class)],
            'reference' => ['required', 'string', 'max:255'],
            'receipt' => ['nullable', 'image', 'max:4096'],
        ];
    }
}
