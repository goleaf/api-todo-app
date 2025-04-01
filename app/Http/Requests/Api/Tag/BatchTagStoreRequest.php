<?php

namespace App\Http\Requests\Api\Tag;

use App\Http\Requests\ApiRequest;

class BatchTagStoreRequest extends ApiRequest
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
            'tags' => 'required|array|min:1',
            'tags.*.name' => 'required|string|min:1|max:50',
            'tags.*.color' => 'sometimes|string|max:20',
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
            'tags.min' => __('validation.min.array', ['attribute' => 'tags', 'min' => 1]),
            'tags.*.name.required' => __('validation.required', ['attribute' => 'tag name']),
            'tags.*.name.string' => __('validation.string', ['attribute' => 'tag name']),
            'tags.*.name.min' => __('validation.min.string', ['attribute' => 'tag name', 'min' => 1]),
            'tags.*.name.max' => __('validation.max.string', ['attribute' => 'tag name', 'max' => 50]),
            'tags.*.color.string' => __('validation.string', ['attribute' => 'tag color']),
            'tags.*.color.max' => __('validation.max.string', ['attribute' => 'tag color', 'max' => 20]),
        ];
    }
} 