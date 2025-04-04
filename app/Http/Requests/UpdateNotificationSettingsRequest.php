<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationSettingsRequest extends FormRequest
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
            'email_notifications' => 'boolean',
            'due_date_reminders' => 'boolean',
            'reminder_time' => 'required|string|date_format:H:i',
            'browser_notifications' => 'boolean',
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'reminder_time.required' => trans('validation.required', ['attribute' => 'reminder time']),
            'reminder_time.date_format' => trans('validation.date_format', ['attribute' => 'reminder time', 'format' => 'HH:MM']),
        ];
    }
} 