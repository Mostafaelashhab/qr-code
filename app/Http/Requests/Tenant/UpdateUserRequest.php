<?php

namespace App\Http\Requests\Tenant;

use App\Http\Requests\Concerns\ResolvesStaffRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_ref' => ['required', $this->staffRoleRule()],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }

    /**
     * Validated attributes ready for a model update, dropping an empty password
     * and resolving role_ref into the role + role_id columns.
     *
     * @return array<string, mixed>
     */
    public function userAttributes(): array
    {
        $data = [
            ...$this->safe()->only(['name', 'email', 'phone', 'is_active']),
            ...$this->resolvedRole(),
        ];

        if (filled($this->input('password'))) {
            $data['password'] = $this->string('password')->toString();
        }

        return $data;
    }
}
