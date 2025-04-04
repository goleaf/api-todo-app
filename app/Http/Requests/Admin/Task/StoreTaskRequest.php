<?php

namespace App\Http\Requests\Admin\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
            'estimated_time' => ['nullable', 'integer', 'min:1'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }
} 