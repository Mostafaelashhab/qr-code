<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
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
        $clientId = $this->user()->client_id;

        return [
            'student_id' => [
                'required',
                Rule::exists('students', 'id')->where('client_id', $clientId),
                Rule::unique('enrollments', 'student_id')->where('group_id', $this->route('group')->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.unique' => __('students.already_enrolled'),
        ];
    }
}
