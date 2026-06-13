<?php

namespace App\Http\Requests\Tenant;

use App\Enums\AttendanceStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
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
            'session_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
            'statuses' => ['required', 'array'],
            'statuses.*' => [Rule::enum(AttendanceStatus::class)],
        ];
    }
}
