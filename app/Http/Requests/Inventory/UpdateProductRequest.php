<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $product = $this->route('product');
        $id = is_object($product) ? $product->id : $product;

        return [
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'uom_id' => ['required', 'exists:units_of_measurement,id'],
            'preferred_supplier_id' => ['nullable', 'exists:suppliers,id'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'max_stock' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'uom_id.required' => 'Unit of measurement is required.',
            'uom_id.exists' => 'The selected unit of measurement is invalid.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.min' => 'Selling price must be 0 or higher.',
            'sku.unique' => 'This product code / SKU is already registered.',
            'image.image' => 'The uploaded file must be a valid image.',
            'image.mimes' => 'Product image must be in JPG, JPEG, PNG, or WEBP format.',
            'image.max' => 'Product image size cannot exceed 2 MB.',
        ];
    }
}
