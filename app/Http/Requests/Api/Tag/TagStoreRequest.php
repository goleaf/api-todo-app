<?php

namespace App\Http\Requests\Api\Tag;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class TagStoreRequest extends ApiRequest
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
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'color' => 'sometimes|string|max:20',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.required', ['attribute' => 'name']),
            'name.unique' => __('messages.tag.duplicate'),
            'color.max' => __('validation.max.string', ['attribute' => 'color', 'max' => 20]),
        ];
    }
} 