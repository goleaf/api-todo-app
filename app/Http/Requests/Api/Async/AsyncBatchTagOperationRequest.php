<?php

namespace App\Http\Requests\Api\Async;

use App\Http\Requests\ApiRequest;

class AsyncBatchTagOperationRequest extends ApiRequest
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
            'tags' => 'required|array|min:1',
            'tags.*' => 'string|max:50',
            'operation' => 'required|string|in:add,remove',
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
            'tags.required' => __('validation.required', ['attribute' => 'tags']),
            'tags.array' => __('validation.array', ['attribute' => 'tags']),
            'tags.min' => __('validation.min.array', ['attribute' => 'tags', 'min' => 1]),
            'tags.*.string' => __('validation.string', ['attribute' => 'tag']),
            'tags.*.max' => __('validation.max.string', ['attribute' => 'tag', 'max' => 50]),
            'operation.required' => __('validation.required', ['attribute' => 'operation']),
            'operation.string' => __('validation.string', ['attribute' => 'operation']),
            'operation.in' => __('validation.in', ['attribute' => 'operation']),
        ];
    }
} 