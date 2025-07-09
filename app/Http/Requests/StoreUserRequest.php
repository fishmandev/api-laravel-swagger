<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Store User Request
 * 
 * Validates and authorizes requests for creating new users.
 * Enforces business rules and data integrity constraints.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     * 
     * Controls access to the user creation endpoint. Currently allows
     * all authenticated requests, but can be modified to implement
     * role-based permissions.
     * 
     * @return bool True if request is authorized, false otherwise
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     * 
     * Defines validation rules for creating a new user. All rules are
     * designed to ensure data integrity and security.
     * 
     * @return array<string, ValidationRule|array<mixed>|string> Validation rules
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$/', // At least one lowercase, uppercase, and digit
            ],
            'dob' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01',
            ],
            'is_active' => [
                'boolean',
            ],
            'level' => [
                'integer',
                'min:1',
                'max:100',
            ],
            'rating' => [
                'nullable',
                'numeric',
                'min:0',
                'max:10',
            ],
            'metadata' => [
                'nullable',
                'array',
                'max:10', // Maximum 10 metadata keys
            ],
            'metadata.*' => [
                'string',
                'max:1000', // Maximum 1000 characters per metadata value
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules
     * 
     * @return array<string, string> Custom error messages
     */
    public function messages(): array
    {
        return [
            'name.regex' => 'Name must contain only letters and spaces.',
            'email.email' => 'Please provide a valid email address.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one digit.',
            'dob.before' => 'Date of birth must be before today.',
            'dob.after' => 'Date of birth must be after January 1, 1900.',
            'level.min' => 'Level must be at least 1.',
            'level.max' => 'Level cannot exceed 100.',
            'rating.min' => 'Rating must be at least 0.',
            'rating.max' => 'Rating cannot exceed 10.',
            'metadata.max' => 'Metadata cannot have more than 10 fields.',
            'metadata.*.max' => 'Each metadata value cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors
     * 
     * @return array<string, string> Custom attribute names
     */
    public function attributes(): array
    {
        return [
            'dob' => 'date of birth',
            'is_active' => 'active status',
        ];
    }

    /**
     * Configure the validator instance
     * 
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation logic can be added here
            if ($this->input('level') && $this->input('rating')) {
                if ($this->input('level') < 10 && $this->input('rating') > 5) {
                    $validator->errors()->add('rating', 'Users below level 10 cannot have rating above 5.');
                }
            }
        });
    }
}
