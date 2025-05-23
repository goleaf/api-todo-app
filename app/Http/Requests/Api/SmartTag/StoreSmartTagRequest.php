<?php

namespace App\Http\Requests\Api\SmartTag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreSmartTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('smart_tags')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'description' => ['nullable', 'string'],
            'conditions' => ['required', 'array'],
            'conditions.*.field' => [
                'required', 
                'string', 
                'in:title,description,due_date,priority,status,has_tags,has_time_entries,has_attachments,created_at,updated_at,specific_tag'
            ],
            'conditions.*.operator' => ['required', 'string', 'in:equals,contains,starts_with,ends_with,greater_than,less_than,between'],
            'conditions.*.value' => ['required'],
            'actions' => ['required', 'array'],
            'actions.*.type' => [
                'required', 
                'string', 
                'in:add_tag,remove_tag,set_category,set_priority,set_status,set_due_date,mark_completed,mark_incomplete'
            ],
            'actions.*.tag_id' => ['required_if:actions.*.type,add_tag,remove_tag', 'exists:tags,id'],
            'actions.*.category_id' => ['required_if:actions.*.type,set_category', 'exists:categories,id'],
            'actions.*.priority' => ['required_if:actions.*.type,set_priority', 'integer', 'in:1,2,3'],
            'actions.*.status' => ['required_if:actions.*.type,set_status', 'string', 'in:pending,in_progress,completed'],
            'actions.*.value' => ['required_if:actions.*.type,set_due_date', 'string'],
            'is_active' => ['boolean'],
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
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
} 