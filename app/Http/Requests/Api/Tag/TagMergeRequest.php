<?php

namespace App\Http\Requests\Api\Tag;

use App\Http\Requests\ApiRequest;

class TagMergeRequest extends ApiRequest
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
            'source_tag_id' => 'required|integer|exists:tags,id',
            'target_tag_id' => 'required|integer|exists:tags,id|different:source_tag_id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'source_tag_id.required' => __('validation.required', ['attribute' => 'source tag']),
            'source_tag_id.integer' => __('validation.integer', ['attribute' => 'source tag']),
            'source_tag_id.exists' => __('validation.exists', ['attribute' => 'source tag']),
            'target_tag_id.required' => __('validation.required', ['attribute' => 'target tag']),
            'target_tag_id.integer' => __('validation.integer', ['attribute' => 'target tag']),
            'target_tag_id.exists' => __('validation.exists', ['attribute' => 'target tag']),
            'target_tag_id.different' => __('validation.different', ['attribute' => 'target tag', 'other' => 'source tag']),
        ];
    }
} 