<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'category_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $category = \App\Models\Category::find($value);
                        if (! $category || $category->user_id !== Auth::id()) {
                            $fail('The selected category is invalid.');
                        }
                    }
                },
            ],
            'priority' => ['nullable', 'string', 'in:low,medium,high,0,1,2'],
            'completed' => ['nullable', 'boolean'],
            'session_id' => ['nullable', 'string', 'max:255'],
            'reminder_at' => ['nullable', 'date'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The task title is required.',
            'title.max' => 'The task title may not be greater than :max characters.',
            'due_date.date' => 'The due date must be a valid date.',
            'category_id.exists' => 'The selected category is invalid.',
            'priority.in' => 'The priority must be low, medium, high, or a number between 0-2.',
            'completed.boolean' => 'The completed field must be true or false.',
            'reminder_at.date' => 'The reminder date must be a valid date.',
            'tags.array' => 'Tags must be provided as an array.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Tags cannot be longer than :max characters.',
            'progress.integer' => 'Progress must be an integer.',
            'progress.min' => 'Progress cannot be less than :min.',
            'progress.max' => 'Progress cannot be more than :max.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert string tag input to array if needed
        if ($this->has('tags') && is_string($this->tags)) {
            $this->merge([
                'tags' => array_map('trim', explode(',', $this->tags)),
            ]);
        }

        // Convert priority from string to integer if needed
        if ($this->has('priority') && is_numeric($this->priority)) {
            $this->merge([
                'priority' => (int) $this->priority,
            ]);
        }

        // Auto-set progress to 100 if completing a task
        if ($this->has('completed') && $this->completed) {
            $this->merge([
                'progress' => 100,
            ]);
        }
    }
}
