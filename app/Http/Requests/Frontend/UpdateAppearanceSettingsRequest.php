<?php

namespace App\Http\Requests\Frontend;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'theme' => ['required', 'string', 'in:light,dark,auto'],
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'sidebar_collapsed' => ['sometimes', 'boolean'],
            'dense_mode' => ['sometimes', 'boolean'],
        ];
    }
} 