<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SmartTagRequest extends FormRequest
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
        $smartTagId = $this->route('smart_tag') ? $this->route('smart_tag')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('smart_tags')
                    ->where('user_id', $userId)
                    ->ignore($smartTagId)
            ],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'criteria' => ['required', 'array'],
            'criteria.*.field' => ['required', 'string', 'in:title,description,status,priority,due_date,category_id,created_at,updated_at'],
            'criteria.*.operator' => ['required', 'string', 'in:=,!=,>,<,>=,<=,contains,not_contains,starts_with,ends_with,between,in,not_in,is_null,is_not_null'],
            'criteria.*.value' => ['nullable'],
            'match_type' => ['required', 'string', 'in:all,any'],
        ];
    }
} 