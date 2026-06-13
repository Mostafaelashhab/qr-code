<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
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
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],

            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'owner_phone' => ['nullable', 'string', 'max:50'],
            'owner_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array{name: string, email: ?string, phone: ?string, address: ?string, is_active: bool}
     */
    public function clientData(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'address' => $this->input('address'),
            'is_active' => $this->boolean('is_active'),
        ];
    }

    /**
     * @return array{name: string, email: string, phone: ?string, password: string}
     */
    public function ownerData(): array
    {
        return [
            'name' => $this->string('owner_name')->toString(),
            'email' => $this->string('owner_email')->toString(),
            'phone' => $this->input('owner_phone'),
            'password' => $this->string('owner_password')->toString(),
        ];
    }
}
