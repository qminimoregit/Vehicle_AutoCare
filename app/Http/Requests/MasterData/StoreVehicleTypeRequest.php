<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleTypeRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100', 'unique:vehicle_types,name'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'enable_carpets' => ['nullable', 'boolean'],
            'carpet_categories' => ['nullable', 'array'],
            'carpet_categories.*' => ['exists:carpet_categories,id'],
            'new_carpet_category' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Vehicle type name is required.',
            'name.unique' => 'This vehicle type already exists.',
            'image.image' => 'Please select a valid image.',
            'image.mimes' => 'Vehicle image must be a file of type: jpg, jpeg, png, webp.',
            'image.max' => 'Vehicle image size cannot exceed 2 MB.',
        ];
    }
}
