<?php

namespace App\Http\Requests\Api\Async;

use App\Http\Requests\Api\ApiRequest;

class AsyncBulkProcessTasksRequest extends ApiRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'integer|exists:tasks,id',
            'action' => 'required|string|in:complete,delete,archive',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'task_ids.required' => __('validation.required', ['attribute' => 'task IDs']),
            'task_ids.array' => __('validation.array', ['attribute' => 'task IDs']),
            'task_ids.min' => __('validation.min.array', ['attribute' => 'task IDs', 'min' => 1]),
            'task_ids.*.integer' => __('validation.integer', ['attribute' => 'task ID']),
            'task_ids.*.exists' => __('validation.exists', ['attribute' => 'task ID']),
            'action.required' => __('validation.required', ['attribute' => 'action']),
            'action.string' => __('validation.string', ['attribute' => 'action']),
            'action.in' => __('validation.in', ['attribute' => 'action']),
        ];
    }
} 