<?php

namespace App\Http\Requests\Tenant;

use App\Enums\Permission;
use App\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('client_id', $this->user()->client_id)
                    ->ignore($role->id),
            ],
            'permissions' => ['array'],
            'permissions.*' => [Rule::enum(Permission::class)],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function roleAttributes(): array
    {
        return ['name' => $this->string('name')->toString()];
    }

    /**
     * The selected permission values.
     *
     * @return array<int, string>
     */
    public function permissions(): array
    {
        return array_values($this->input('permissions', []));
    }
}
