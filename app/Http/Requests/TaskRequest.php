<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
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
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array|string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'due_date' => ['nullable', 'date', 'after:now'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'in_progress', 'completed'])],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => trans('validation.custom.title.required'),
            'title.max' => trans('validation.custom.title.max'),
            'due_date.date' => trans('validation.custom.due_date.date'),
            'due_date.after' => trans('validation.custom.due_date.after'),
            'priority.required' => trans('validation.custom.priority.required'),
            'priority.in' => trans('validation.custom.priority.in'),
            'status.in' => trans('validation.custom.status.in'),
        ];
    }
} 