<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tasks')->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                        ->where('title', $this->title);
                }),
            ],
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|date_format:Y-m-d',
            'priority' => 'nullable|integer|between:1,4',
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.required', ['attribute' => 'title']),
            'title.unique' => __('messages.task.duplicate'),
            'priority.between' => __('messages.task.invalid_priority'),
            'due_date.date_format' => __('messages.task.invalid_date'),
            'category_id.exists' => __('messages.category.not_found'),
        ];
    }
} 