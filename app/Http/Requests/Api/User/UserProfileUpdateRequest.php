<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;

class UserProfileUpdateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.auth()->id(),
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.user.name_required'),
            'email.required' => __('validation.user.email_required'),
            'email.email' => __('validation.user.email_invalid'),
            'email.unique' => __('validation.user.email_unique'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'email' => __('validation.attributes.email'),
        ];
    }
}
