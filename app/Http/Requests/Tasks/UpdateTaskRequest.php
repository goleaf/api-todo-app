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
                        if (!$category || $category->user_id !== Auth::id()) {
                            $fail('The selected category is invalid.');
                        }
                    }
                }
            ],
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'completed' => ['nullable', 'boolean'],
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
            'priority.in' => 'The priority must be low, medium, or high.',
            'completed.boolean' => 'The completed field must be true or false.',
        ];
    }
} 