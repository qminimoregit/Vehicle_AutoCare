<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleTypeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vehicleType = $this->route('vehicleType') ?? $this->route('vehicle_type');
        $id = is_object($vehicleType) ? $vehicleType->id : $vehicleType;

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('vehicle_types', 'name')->ignore($id)],
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
            'name.required' => 'Vehicle Type Name is required.',
            'name.unique' => 'This vehicle type name is already in use.',
            'status.required' => 'Please select whether the vehicle type is Active or Inactive.',
            'status.in' => 'Status must be either Active or Inactive.',
        ];
    }
}
