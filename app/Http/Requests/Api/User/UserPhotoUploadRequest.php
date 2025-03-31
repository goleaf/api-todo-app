<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;

class UserPhotoUploadRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'photo.required' => __('validation.user.photo_required'),
            'photo.image' => __('validation.user.photo_image'),
            'photo.mimes' => __('validation.user.photo_mimes'),
            'photo.max' => __('validation.user.photo_max'),
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
