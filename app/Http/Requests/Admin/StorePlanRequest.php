<?php

namespace App\Http\Requests\Admin;

use App\Enums\BillingPeriod;
use App\Enums\Feature;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isSuperAdmin();
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_period' => ['required', new Enum(BillingPeriod::class)],
            'max_users' => ['nullable', 'integer', 'min:1'],
            'max_students' => ['nullable', 'integer', 'min:1'],
            'features' => ['nullable', 'array'],
            'features.*' => [Rule::in(Feature::allValues())],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'features' => array_values(array_filter(
                (array) $this->input('features', []),
                fn ($feature): bool => filled($feature),
            )),
        ]);
    }
}
