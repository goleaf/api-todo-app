<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
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
        $userId = Auth::id();
        $tagId = $this->route('tag') ? $this->route('tag')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags')
                    ->where('user_id', $userId)
                    ->ignore($tagId)
            ],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }
} 