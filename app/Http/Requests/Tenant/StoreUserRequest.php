<?php

namespace App\Http\Requests\Tenant;

use App\Http\Requests\Concerns\ResolvesStaffRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    use ResolvesStaffRole;

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_ref' => ['required', $this->staffRoleRule()],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active', true)]);
    }

    /**
     * Validated attributes ready for creating the user, with role_ref resolved.
     *
     * @return array<string, mixed>
     */
    public function userAttributes(): array
    {
        return [
            ...$this->safe()->only(['name', 'email', 'phone', 'password', 'is_active']),
            ...$this->resolvedRole(),
        ];
    }
}
