<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTestRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'group_id' => ['required', Rule::exists('groups', 'id')->where('client_id', $this->user()->client_id)],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:600'],
            'available_from' => ['nullable', 'date'],
            'available_to' => ['nullable', 'date', 'after_or_equal:available_from'],
            'shuffle' => ['boolean'],
            'show_results' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'shuffle' => $this->boolean('shuffle'),
            'show_results' => $this->boolean('show_results'),
        ]);
    }
}
