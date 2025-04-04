<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|array|string>>
     */
    public function rules(): array
    {
        $tagId = $this->route('tag') ? $this->route('tag')->id : null;
        
        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                'unique:tags,name,' . $tagId . ',id,user_id,' . Auth::id()
            ],
            'description' => ['nullable', 'string'],
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => trans('validation.custom.name.required'),
            'name.max' => trans('validation.custom.name.max'),
            'name.unique' => trans('validation.custom.name.unique'),
        ];
    }
} 