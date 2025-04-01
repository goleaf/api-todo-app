<?php

namespace App\Http\Requests\Api\Tag;

use App\Http\Requests\ApiRequest;

class TagSuggestionsRequest extends ApiRequest
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
            'query' => 'required|string|min:1|max:50',
            'limit' => 'sometimes|integer|min:1|max:50',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.required' => __('validation.required', ['attribute' => 'query']),
            'query.string' => __('validation.string', ['attribute' => 'query']),
            'query.min' => __('validation.min.string', ['attribute' => 'query', 'min' => 1]),
            'query.max' => __('validation.max.string', ['attribute' => 'query', 'max' => 50]),
            'limit.integer' => __('validation.integer', ['attribute' => 'limit']),
            'limit.min' => __('validation.min.numeric', ['attribute' => 'limit', 'min' => 1]),
            'limit.max' => __('validation.max.numeric', ['attribute' => 'limit', 'max' => 50]),
        ];
    }
} 