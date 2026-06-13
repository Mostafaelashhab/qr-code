<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class StoreGroupRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['required', $this->existsInTenant('subjects')],
            'teacher_id' => ['nullable', $this->existsInTenant('teachers')],
            'monthly_fee' => ['required', 'numeric', 'min:0'],
            'teacher_share' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active', true)]);
    }

    protected function existsInTenant(string $table): Exists
    {
        return Rule::exists($table, 'id')->where('client_id', $this->user()->client_id);
    }
}
