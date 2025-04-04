<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppearanceSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'theme' => 'required|string|in:light,dark,auto',
            'primary_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'sidebar_collapsed' => 'boolean',
            'dense_mode' => 'boolean',
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'theme.required' => trans('validation.required', ['attribute' => 'theme']),
            'theme.in' => trans('validation.in', ['attribute' => 'theme']),
            'primary_color.required' => trans('validation.required', ['attribute' => 'primary color']),
            'primary_color.regex' => trans('validation.regex', ['attribute' => 'primary color']),
        ];
    }
} 