<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'category_id' => [
                'nullable', 
                'integer', 
                'exists:categories,id,user_id,' . Auth::id()
            ],
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'status' => ['sometimes', 'string', 'in:pending,in_progress,completed'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['exists:tags,id,user_id,' . Auth::id()],
            'estimated_time' => ['nullable', 'integer', 'min:1'],
        ];
    }
} 