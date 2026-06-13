<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'center_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array{name: string, email: ?string, phone: ?string, is_active: bool}
     */
    public function clientData(): array
    {
        return [
            'name' => $this->string('center_name')->toString(),
            'email' => $this->input('email'),
            'phone' => $this->input('phone'),
            'is_active' => true,
        ];
    }

    /**
     * @return array{name: string, email: string, phone: ?string, password: string}
     */
    public function ownerData(): array
    {
        return [
            'name' => $this->string('name')->toString(),
            'email' => $this->string('email')->toString(),
            'phone' => $this->input('phone'),
            'password' => $this->string('password')->toString(),
        ];
    }
}
