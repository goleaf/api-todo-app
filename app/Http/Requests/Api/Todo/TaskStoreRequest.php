<?php

namespace App\Http\Requests\Api\Todo;

use App\Http\Requests\ApiRequest;

class TaskStoreRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'nullable|integer|in:0,1,2',
            'reminder_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'progress' => 'nullable|integer|min:0|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.task.title_required'),
            'title.max' => __('validation.task.title_max'),
            'category_id.exists' => __('validation.task.category_exists'),
            'priority.in' => __('validation.task.priority_invalid'),
            'progress.min' => __('validation.task.progress_min'),
            'progress.max' => __('validation.task.progress_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => __('validation.attributes.title'),
            'description' => __('validation.attributes.description'),
            'due_date' => __('validation.attributes.due_date'),
            'category_id' => __('validation.attributes.category'),
            'priority' => __('validation.attributes.priority'),
            'reminder_at' => __('validation.attributes.reminder'),
            'tags' => __('validation.attributes.tags'),
            'progress' => __('validation.attributes.progress'),
        ];
    }
} 