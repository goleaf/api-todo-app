<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Hash;

class UserPasswordUpdateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (! Hash::check($value, auth()->user()->password)) {
                    $fail(__('validation.user.current_password_invalid'));
                }
            }],
            'password' => 'required|string|min:8|confirmed|different:current_password|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => __('validation.user.current_password_required'),
            'password.required' => __('validation.user.password_required'),
            'password.min' => __('validation.user.password_min'),
            'password.confirmed' => __('validation.user.password_confirmed'),
            'password.different' => __('validation.user.password_different'),
            'password.regex' => __('validation.user.password_regex', ['min' => 8]),
            'password_confirmation.required' => __('validation.user.password_confirmation_required'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'current_password' => __('validation.attributes.current_password'),
            'password' => __('validation.attributes.password'),
        ];
    }
}
