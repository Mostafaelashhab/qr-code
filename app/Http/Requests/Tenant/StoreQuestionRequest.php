<?php

namespace App\Http\Requests\Tenant;

use App\Enums\QuestionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreQuestionRequest extends FormRequest
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
            'body' => ['required', 'string', 'max:1000'],
            'type' => ['required', Rule::enum(QuestionType::class)],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'options' => ['array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'correct' => ['required'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($this->enum('type', QuestionType::class) !== QuestionType::Mcq) {
                return;
            }

            $options = (array) $this->input('options', []);
            $correct = $this->input('correct');

            if (collect($options)->filter(fn ($o): bool => filled($o))->count() < 2) {
                $validator->errors()->add('options', __('tests.need_two_options'));
            }

            if (! isset($options[$correct]) || blank($options[$correct])) {
                $validator->errors()->add('correct', __('tests.choose_correct'));
            }
        });
    }
}
