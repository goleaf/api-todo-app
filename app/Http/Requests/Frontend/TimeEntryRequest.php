<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TimeEntryRequest extends FormRequest
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
        $rules = [
            'task_id' => [
                'nullable',
                'integer',
                'exists:tasks,id,user_id,' . Auth::id()
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ];
        
        // If this is a manual time entry add these rules
        if ($this->input('entry_type') === 'manual') {
            $rules = array_merge($rules, [
                'start_time' => ['required', 'date'],
                'end_time' => ['required', 'date', 'after:start_time'],
            ]);
        }
        
        return $rules;
    }
} 