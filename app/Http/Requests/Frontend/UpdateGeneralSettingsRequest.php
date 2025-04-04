<?php

namespace App\Http\Requests\Frontend;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'language' => ['required', 'string', 'in:' . implode(',', config('app.available_locales', ['en']))],
            'timezone' => ['required', 'string', 'timezone'],
            'date_format' => ['required', 'string', 'in:Y-m-d,m/d/Y,d/m/Y,d.m.Y'],
            'time_format' => ['required', 'string', 'in:H:i,h:i A'],
        ];
    }
} 