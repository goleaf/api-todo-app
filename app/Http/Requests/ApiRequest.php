<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

abstract class ApiRequest extends FormRequest
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
     */
    abstract public function rules(): array;

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('validation.failed'),
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => __('auth.unauthorized'),
            ], JsonResponse::HTTP_FORBIDDEN)
        );
    }
}
