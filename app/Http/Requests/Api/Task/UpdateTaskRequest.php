<?php

namespace App\Http\Requests\Api\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $task = $this->route('task');
        return $task && $task->user_id === Auth::id();
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
            'due_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:today'],
            'category_id' => [
                'sometimes',
                'nullable', 
                'integer', 
                'exists:categories,id,user_id,' . Auth::id()
            ],
            'priority' => ['sometimes', 'required', 'string', 'in:low,medium,high'],
            'status' => ['sometimes', 'string', 'in:pending,in_progress,completed'],
            'estimated_time' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['exists:tags,id,user_id,' . Auth::id()],
        ];
    }
    
    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
} 