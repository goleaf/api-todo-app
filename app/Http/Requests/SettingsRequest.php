<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_url' => 'required|url',
            'timezone' => 'required|string|timezone',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:20',
            'registration_enabled' => 'boolean',
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
            'site_name.required' => trans('validation.required', ['attribute' => 'site name']),
            'site_url.required' => trans('validation.required', ['attribute' => 'site URL']),
            'site_url.url' => trans('validation.url', ['attribute' => 'site URL']),
            'timezone.required' => trans('validation.required', ['attribute' => 'timezone']),
            'timezone.timezone' => trans('validation.timezone', ['attribute' => 'timezone']),
            'date_format.required' => trans('validation.required', ['attribute' => 'date format']),
            'time_format.required' => trans('validation.required', ['attribute' => 'time format']),
        ];
    }
} 