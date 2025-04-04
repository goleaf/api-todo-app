<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneralSettingsRequest extends FormRequest
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
            'language' => 'required|string|in:en,fr,es,de,it',
            'timezone' => 'required|string|timezone',
            'date_format' => 'required|string|in:Y-m-d,m/d/Y,d/m/Y,d.m.Y',
            'time_format' => 'required|string|in:H:i,h:i A',
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
            'language.required' => trans('validation.required', ['attribute' => 'language']),
            'language.in' => trans('validation.in', ['attribute' => 'language']),
            'timezone.required' => trans('validation.required', ['attribute' => 'timezone']),
            'timezone.timezone' => trans('validation.timezone', ['attribute' => 'timezone']),
            'date_format.required' => trans('validation.required', ['attribute' => 'date format']),
            'date_format.in' => trans('validation.in', ['attribute' => 'date format']),
            'time_format.required' => trans('validation.required', ['attribute' => 'time format']),
            'time_format.in' => trans('validation.in', ['attribute' => 'time format']),
        ];
    }
} 