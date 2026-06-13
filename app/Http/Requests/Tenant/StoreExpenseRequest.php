<?php

namespace App\Http\Requests\Tenant;

use App\Enums\ExpenseCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreExpenseRequest extends FormRequest
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
            'category' => ['required', new Enum(ExpenseCategory::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
