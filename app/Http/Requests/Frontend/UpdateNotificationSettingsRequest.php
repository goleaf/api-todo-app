<?php

namespace App\Http\Requests\Frontend;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email_notifications' => ['required', 'boolean'],
            'due_date_reminders' => ['required', 'boolean'],
            'reminder_time' => ['required', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'browser_notifications' => ['required', 'boolean'],
        ];
    }
} 