<?php

namespace App\Http\Requests\Api\SmartTag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateSmartTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('smartTag'));
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
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('smart_tags')->where(function ($query) {
                    return $query->where('user_id', auth()->id())
                                ->where('id', '!=', $this->route('smartTag')->id);
                }),
            ],
            'description' => ['nullable', 'string'],
            'conditions' => ['sometimes', 'required', 'array'],
            'conditions.*.field' => ['required', 'string', 'in:title,description,due_date,priority,status'],
            'conditions.*.operator' => ['required', 'string', 'in:equals,contains,starts_with,ends_with,greater_than,less_than,between'],
            'conditions.*.value' => ['required'],
            'actions' => ['sometimes', 'required', 'array'],
            'actions.*.type' => ['required', 'string', 'in:add_tag,remove_tag,set_category,set_priority,set_status'],
            'actions.*.tag_id' => ['required_if:actions.*.type,add_tag,remove_tag', 'exists:tags,id'],
            'actions.*.category_id' => ['required_if:actions.*.type,set_category', 'exists:categories,id'],
            'actions.*.priority' => ['required_if:actions.*.type,set_priority', 'integer', 'in:1,2,3'],
            'actions.*.status' => ['required_if:actions.*.type,set_status', 'string', 'in:todo,in_progress,completed'],
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