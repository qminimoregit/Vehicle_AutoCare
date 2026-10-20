<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceVehiclePricesRequest extends FormRequest
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
            'prices' => ['nullable', 'array'],
            'prices.*.price' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'prices.*.cost' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prices.*.price.numeric' => 'Please enter a valid service price.',
            'prices.*.price.min' => 'Service price cannot be negative.',
            'prices.*.price.max' => 'Service price exceeds the maximum permitted amount.',
            'prices.*.cost.numeric' => 'Please enter a valid service cost.',
            'prices.*.cost.min' => 'Service cost cannot be negative.',
            'prices.*.cost.max' => 'Service cost exceeds the maximum permitted amount.',
        ];
    }
}
