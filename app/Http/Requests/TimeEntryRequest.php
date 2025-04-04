<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array
     */
    public function rules()
    {
        return [
            'task_id' => 'required|exists:tasks,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string|max:255',
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        // If we're updating an existing time entry and don't have date/times in the request,
        // we'll extract them from the existing entry
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $timeEntry = $this->route('timeEntry');
            
            if ($timeEntry && !$this->has('date') && $timeEntry->started_at) {
                $this->merge([
                    'date' => $timeEntry->started_at->format('Y-m-d'),
                ]);
            }
            
            if ($timeEntry && !$this->has('start_time') && $timeEntry->started_at) {
                $this->merge([
                    'start_time' => $timeEntry->started_at->format('H:i'),
                ]);
            }
            
            if ($timeEntry && !$this->has('end_time') && $timeEntry->ended_at) {
                $this->merge([
                    'end_time' => $timeEntry->ended_at->format('H:i'),
                ]);
            }
        }
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'task_id.required' => trans('validation.custom.task_id.required'),
            'task_id.exists' => trans('validation.custom.task_id.exists'),
            'date.required' => trans('validation.custom.date.required'),
            'date.date' => trans('validation.custom.date.date'),
            'start_time.required' => trans('validation.custom.start_time.required'),
            'start_time.date_format' => trans('validation.custom.start_time.date_format'),
            'end_time.required' => trans('validation.custom.end_time.required'),
            'end_time.date_format' => trans('validation.custom.end_time.date_format'),
            'end_time.after' => trans('validation.custom.end_time.after'),
            'description.max' => trans('validation.custom.description.max'),
        ];
    }
} 