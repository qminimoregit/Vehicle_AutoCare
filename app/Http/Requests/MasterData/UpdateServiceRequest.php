<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
        $service = $this->route('service');
        $serviceId = is_object($service) ? $service->id : $service;

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('services', 'code')->ignore($serviceId)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Service Code is required.',
            'code.unique' => 'This service code is already in use by another service.',
            'name.required' => 'Service Name is required.',
            'status.required' => 'Please select whether the service is Active or Inactive.',
            'status.in' => 'Status must be either Active or Inactive.',
        ];
    }
}
