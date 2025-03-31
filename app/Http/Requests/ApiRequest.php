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
     * By default, all API requests are authorized and individual requests
     * should override this method if authorization is needed.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * This method must be implemented by all child classes.
     */
    abstract public function rules(): array;

    /**
     * Get custom error messages for validator errors.
     * This can be overridden by child classes for custom messages.
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     * This can be overridden by child classes for custom attributes.
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Handle a failed validation attempt.
     * For API requests, we return a JSON response instead of redirecting.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => __('messages.validation_error'),
            'errors' => $validator->errors(),
        ], 422));
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
