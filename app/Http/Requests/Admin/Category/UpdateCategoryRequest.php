<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', $this->user_id);
                })->ignore($this->category->id),
            ],
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string|max:255',
            'user_id' => 'required|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category name already exists for the selected user.',
            'user_id.required' => 'The user must be specified.',
            'user_id.exists' => 'The selected user does not exist.',
        ];
    }
} 