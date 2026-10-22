<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompanyDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'tax_number' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'signature' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'remove_signature' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Company Name is required.',
            'email.email' => 'Please provide a valid company email address.',
            'logo.image' => 'The logo must be a valid image file.',
            'logo.mimes' => 'The logo must be a file of type: png, jpg, jpeg, svg, webp.',
            'logo.max' => 'The logo size must not exceed 2MB (2048 KB).',
            'signature.image' => 'The authorized signature must be a valid image file.',
            'signature.mimes' => 'The authorized signature must be a file of type: png, jpg, jpeg, svg, webp.',
            'signature.max' => 'The authorized signature size must not exceed 2MB (2048 KB).',
        ];
    }
}
