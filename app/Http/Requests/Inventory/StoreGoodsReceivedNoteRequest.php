<?php

namespace App\Http\Requests\Inventory;

use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGoodsReceivedNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'supplier_invoice_number' => ['nullable', 'string', 'max:100'],
            'received_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'invoice_attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'action' => ['nullable', 'in:draft,confirm'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.purchase_quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.free_quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.purchase_unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.selling_price_snapshot' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier is invalid.',
            'received_date.required' => 'Received date is required.',
            'items.required' => 'Please add at least one product item to the GRN.',
            'items.min' => 'Please add at least one product item to the GRN.',
            'items.*.product_id.required' => 'A product must be selected for every line item.',
            'items.*.purchase_quantity.min' => 'Purchased quantity must be greater than zero.',
            'items.*.purchase_unit_price.min' => 'Purchase price cannot be negative.',
        ];
    }

    /**
     * Validate whole-number quantity rules according to UOM.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);
            foreach ($items as $index => $item) {
                if (! empty($item['product_id'])) {
                    $product = Product::with('uom')->find($item['product_id']);
                    if ($product && ! $product->allowsFractionalQuantity()) {
                        if (isset($item['purchase_quantity']) && floor((float) $item['purchase_quantity']) != (float) $item['purchase_quantity']) {
                            $validator->errors()->add(
                                "items.{$index}.purchase_quantity",
                                "Product '{$product->name}' is measured in {$product->uom?->name} and requires a whole-number purchased quantity."
                            );
                        }
                        if (! empty($item['free_quantity']) && floor((float) $item['free_quantity']) != (float) $item['free_quantity']) {
                            $validator->errors()->add(
                                "items.{$index}.free_quantity",
                                "Product '{$product->name}' is measured in {$product->uom?->name} and requires a whole-number free quantity."
                            );
                        }
                    }
                }
            }
        });
    }
}
