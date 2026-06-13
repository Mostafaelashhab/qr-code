<?php

namespace App\Http\Requests\Tenant;

use App\Enums\PaymentMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Exists;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->client_id !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', $this->existsInTenant('students')],
            'group_id' => ['nullable', $this->existsInTenant('groups')],
            'amount' => ['required', 'numeric', 'min:0'],
            'method' => ['required', new Enum(PaymentMethod::class)],
            'for_month' => ['nullable', 'date_format:Y-m'],
            'paid_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function existsInTenant(string $table): Exists
    {
        return Rule::exists($table, 'id')->where('client_id', $this->user()->client_id);
    }
}
