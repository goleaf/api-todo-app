<?php

namespace App\Http\Requests\Api\Category;

use App\Http\Requests\ApiRequest;

class CategoryStoreRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
            'icon' => 'required|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.category.name_required'),
            'name.max' => __('validation.category.name_max'),
            'color.required' => __('validation.category.color_required'),
            'color.regex' => __('validation.category.color_invalid'),
            'icon.required' => __('validation.category.icon_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'color' => __('validation.attributes.color'),
            'icon' => __('validation.attributes.icon'),
        ];
    }
} 