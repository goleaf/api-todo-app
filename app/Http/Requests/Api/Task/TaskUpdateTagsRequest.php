<?php

namespace App\Http\Requests\Api\Task;

use App\Http\Requests\Api\ApiRequest;

class TaskUpdateTagsRequest extends ApiRequest
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
            'tags' => 'required|array',
            'tags.*' => 'string|max:50',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tags.required' => __('validation.required', ['attribute' => 'tags']),
            'tags.array' => __('validation.array', ['attribute' => 'tags']),
            'tags.*.string' => __('validation.string', ['attribute' => 'tag']),
            'tags.*.max' => __('validation.max.string', ['attribute' => 'tag', 'max' => 50]),
        ];
    }
} 