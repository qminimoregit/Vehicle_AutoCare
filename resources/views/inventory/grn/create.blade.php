@extends('layouts.app')

@section('title', 'Create GRN')

@section('content')
    <div 
        class="max-w-6xl mx-auto"
        x-data="{
            isSubmitting: false,
            submitAction: 'draft',
            allProducts: {{ Js::from($products) }},
            items: [
                { product_id: '', uom_name: '-', purchase_unit_price: 0, purchase_quantity: 1, free_quantity: 0, selling_price_snapshot: 0 }
            ],
            init() {
                // If query had supplier, filter preferred products if desired
            },
            addItem() {
                this.items.push({
                    product_id: '',
                    uom_name: '-',
                    purchase_unit_price: 0,
                    purchase_quantity: 1,
                    free_quantity: 0,
                    selling_price_snapshot: 0
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            onProductChange(index) {
                const item = this.items[index];
                const product = this.allProducts.find(p => p.id == item.product_id);
                if (product) {
                    item.uom_name = product.uom ? product.uom.name : '-';
                    item.selling_price_snapshot = product.selling_price || 0;
                } else {
                    item.uom_name = '-';
                    item.selling_price_snapshot = 0;
                }
            },
            lineTotal(item) {
                const qty = parseFloat(item.purchase_quantity) || 0;
                const price = parseFloat(item.purchase_unit_price) || 0;
                return (qty * price).toFixed(2);
            },
            grandTotal() {
                let sum = 0;
                for (let item of this.items) {
                    const qty = parseFloat(item.purchase_quantity) || 0;
                    const price = parseFloat(item.purchase_unit_price) || 0;
                    sum += qty * price;
                }
                return sum.toFixed(2);
            }
        }"
    >
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('inventory.grn.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2">
                &larr; Back to GRN List
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Create Goods Received Note (GRN)</h1>
            <p class="mt-1 text-sm text-gray-500">Record inventory received from a supplier into your care center.</p>
        </div>

        <form 
            method="POST" 
            action="{{ route('inventory.grn.store') }}" 
            enctype="multipart/form-data" 
            class="space-y-6" 
            novalidate
            @submit="if (isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;"
        >
            @csrf
            <input type="hidden" name="action" :value="submitAction">

            <!-- Header Info Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">1. Invoice & Supplier Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Supplier -->
                    <div>
                        <label for="supplier_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Supplier <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <select 
                                name="supplier_id" 
                                id="supplier_id" 
                                required
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white @error('supplier_id') border-rose-400 bg-rose-50/30 @enderror"
                            >
                                <option value="">Select a Supplier...</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $selectedSupplierId) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('supplier_id')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Supplier Invoice Number -->
                    <div>
                        <label for="supplier_invoice_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Supplier Invoice No <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="supplier_invoice_number" 
                                id="supplier_invoice_number" 
                                value="{{ old('supplier_invoice_number') }}" 
                                placeholder="e.g. INV-2026-889"
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                            />
                        </div>
                    </div>

                    <!-- Received Date -->
                    <div>
                        <label for="received_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Received Date <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="date" 
                                name="received_date" 
                                id="received_date" 
                                required 
                                value="{{ old('received_date', date('Y-m-d')) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white @error('received_date') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('received_date')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <!-- Invoice Attachment -->
                    <div>
                        <label for="invoice_attachment" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Upload Supplier Invoice <span class="text-gray-400 font-normal normal-case">(Optional - PDF, JPG, PNG up to 4MB)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="file" 
                                name="invoice_attachment" 
                                id="invoice_attachment" 
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 file:cursor-pointer border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50"
                            />
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Notes / Remarks <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="notes" 
                                id="notes" 
                                value="{{ old('notes') }}" 
                                placeholder="Any delivery notes or dispatch references..."
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">2. Products Received</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Purchased quantity and free promotional units will create separate audit batches.</p>
                    </div>

                    <button 
                        type="button" 
                        @click="addItem()" 
                        class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold rounded-lg text-white bg-[#0B1849] hover:bg-[#15286e] shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product Line
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/80">
                            <tr>
                                <th scope="col" class="px-3 py-2.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-72">
                                    Product *
                                </th>
                                <th scope="col" class="px-2 py-2.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-20">
                                    UOM
                                </th>
                                <th scope="col" class="px-2 py-2.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                    Purchase Price *
                                </th>
                                <th scope="col" class="px-2 py-2.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-24">
                                    Purchased Qty *
                                </th>
                                <th scope="col" class="px-2 py-2.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-24">
                                    Free Qty
                                </th>
                                <th scope="col" class="px-3 py-2.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                    Line Total (LKR)
                                </th>
                                <th scope="col" class="px-2 py-2.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-12">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="hover:bg-gray-50/50">
                                    <!-- Product Dropdown -->
                                    <td class="px-3 py-2">
                                        <select 
                                            :name="`items[${index}][product_id]`" 
                                            x-model="item.product_id"
                                            @change="onProductChange(index)"
                                            required
                                            class="block w-full py-2 px-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                                        >
                                            <option value="">Select product...</option>
                                            <template x-for="p in allProducts" :key="p.id">
                                                <option :value="p.id" x-text="`${p.name} ${p.sku ? '('+p.sku+')' : ''}`"></option>
                                            </template>
                                        </select>
                                    </td>

                                    <!-- UOM Display -->
                                    <td class="px-2 py-2 text-center text-xs text-gray-600 font-semibold" x-text="item.uom_name">
                                    </td>

                                    <!-- Purchase Unit Price -->
                                    <td class="px-2 py-2">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0"
                                            :name="`items[${index}][purchase_unit_price]`" 
                                            x-model.number="item.purchase_unit_price"
                                            required
                                            class="block w-full py-2 px-2.5 text-right font-mono text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                                        />
                                    </td>

                                    <!-- Purchased Quantity -->
                                    <td class="px-2 py-2">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0.01"
                                            :name="`items[${index}][purchase_quantity]`" 
                                            x-model.number="item.purchase_quantity"
                                            required
                                            class="block w-full py-2 px-2.5 text-right font-mono text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                                        />
                                    </td>

                                    <!-- Free Quantity -->
                                    <td class="px-2 py-2">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0"
                                            :name="`items[${index}][free_quantity]`" 
                                            x-model.number="item.free_quantity"
                                            placeholder="0"
                                            class="block w-full py-2 px-2.5 text-right font-mono text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-emerald-50/30"
                                        />
                                    </td>

                                    <!-- Line Total -->
                                    <td class="px-3 py-2 text-right font-mono font-bold text-sm text-gray-900 whitespace-nowrap">
                                        <span x-text="lineTotal(item)"></span>
                                        <input type="hidden" :name="`items[${index}][selling_price_snapshot]`" :value="item.selling_price_snapshot">
                                    </td>

                                    <!-- Remove Button -->
                                    <td class="px-2 py-2 text-center">
                                        <button 
                                            type="button" 
                                            @click="removeItem(index)" 
                                            class="text-rose-500 hover:text-rose-700 p-1 rounded hover:bg-rose-50 transition cursor-pointer"
                                            :disabled="items.length <= 1"
                                            :class="{'opacity-30 cursor-not-allowed': items.length <= 1}"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wider text-gray-700">
                                    Grand Total (Payable to Supplier):
                                </td>
                                <td class="px-3 py-3 text-right font-mono font-black text-base text-gray-900 whitespace-nowrap">
                                    LKR <span x-text="grandTotal()"></span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @error('items')
                    <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Action Footer -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs flex items-center justify-between">
                <a 
                    href="{{ route('inventory.grn.index') }}" 
                    class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-2xs text-center"
                >
                    Cancel
                </a>

                <div class="flex items-center space-x-3">
                    <button 
                        type="submit" 
                        @click="submitAction = 'draft'"
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition shadow-2xs cursor-pointer inline-flex items-center gap-2"
                        :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                    >
                        <svg x-show="isSubmitting && submitAction === 'draft'" class="animate-spin -ml-1 mr-1 h-4 w-4 text-gray-700" fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="(isSubmitting && submitAction === 'draft') ? 'Saving Draft...' : 'Save as Draft'">Save as Draft</span>
                    </button>

                    @if(Auth::user()->isAdmin())
                        <button 
                            type="submit" 
                            @click="if(!confirm('Confirm this GRN and generate stock batches now?')) { $event.preventDefault(); return false; } submitAction = 'confirm';"
                            :disabled="isSubmitting"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 rounded-lg shadow-xs transition cursor-pointer inline-flex items-center gap-2"
                            :class="{'opacity-50 cursor-not-allowed': isSubmitting}"
                        >
                            <svg x-show="isSubmitting && submitAction === 'confirm'" class="animate-spin -ml-1 mr-1 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="(isSubmitting && submitAction === 'confirm') ? 'Confirming...' : 'Confirm & Add to Stock'">Confirm & Add to Stock</span>
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
