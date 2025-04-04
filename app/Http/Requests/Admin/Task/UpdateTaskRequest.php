<?php

namespace App\Http\Requests\Admin\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'user_id' => ['sometimes', 'required', 'exists:users,id'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:categories,id'],
            'priority' => ['sometimes', 'required', 'string', 'in:low,medium,high'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,in_progress,completed'],
            'estimated_time' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
} 