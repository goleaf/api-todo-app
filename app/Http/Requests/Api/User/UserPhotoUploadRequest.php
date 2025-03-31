<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;

class UserPhotoUploadRequest extends ApiRequest
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
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photo.required' => __('validation.required', ['attribute' => 'photo']),
            'photo.image' => __('validation.image', ['attribute' => 'photo']),
            'photo.mimes' => __('validation.mimes', ['attribute' => 'photo', 'values' => 'jpeg, png, jpg, gif']),
            'photo.max' => __('validation.max.file', ['attribute' => 'photo', 'max' => '2MB']),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'photo' => __('validation.attributes.photo'),
        ];
    }
}
