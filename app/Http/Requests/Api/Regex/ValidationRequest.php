<?php

namespace App\Http\Requests\Api\Regex;

use Illuminate\Foundation\Http\FormRequest;

class ValidationRequest extends FormRequest
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
            'value' => 'required|string',
            'type' => 'required|string|in:email,url,ip,ipv4,ipv6,uuid,phone,username,password,hex_color'
        ];
    }
} 