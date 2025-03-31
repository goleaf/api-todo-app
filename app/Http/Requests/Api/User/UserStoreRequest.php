<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'sometimes|string|in:admin,user',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.user.name_required'),
            'name.max' => __('validation.user.name_max'),
            'email.required' => __('validation.user.email_required'),
            'email.email' => __('validation.user.email_invalid'),
            'email.max' => __('validation.user.email_max'),
            'email.unique' => __('validation.user.email_unique'),
            'password.required' => __('validation.user.password_required'),
            'password.confirmed' => __('validation.user.password_confirmed'),
            'role.in' => __('validation.user.role_invalid'),
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
            'password' => __('validation.attributes.password'),
            'role' => __('validation.attributes.role'),
        ];
    }
}
