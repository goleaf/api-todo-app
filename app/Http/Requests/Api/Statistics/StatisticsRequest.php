<?php

namespace App\Http\Requests\Api\Statistics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StatisticsRequest extends FormRequest
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
        $rules = [
            'period' => ['sometimes', 'string', 'in:week,month,year,all'],
        ];

        // Add days validation for productivity trends
        if ($this->is('api/statistics/productivity')) {
            $rules['days'] = ['sometimes', 'integer', 'min:1', 'max:365'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'period.in' => 'The period must be one of: week, month, year, all',
            'days.min' => 'The days parameter must be at least 1',
            'days.max' => 'The days parameter cannot exceed 365',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }
} 