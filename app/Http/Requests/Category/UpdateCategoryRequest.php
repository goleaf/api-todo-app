<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $category = $this->route('category');

        return $this->user()->can('update', $category);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id.',id,user_id,'.$this->user()->id,
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ];
    }
}
