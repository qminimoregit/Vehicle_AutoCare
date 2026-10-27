@extends('layouts.app')

@section('title', 'New Direct Invoice')

@section('content')
<div 
    x-data="invoiceForm({
        services: {{ Js::from($services) }},
        products: {{ Js::from($products) }},
        vehicles: {{ Js::from($vehicles) }},
        customers: {{ Js::from($customers) }},
        selectedCustomerId: '{{ old('customer_id', '') }}',
        oldItems: {{ Js::from(old('items', [])) }}
    })"
    class="max-w-5xl mx-auto space-y-6"
>
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('operations.invoices.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <div class="flex items-center space-x-2">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Create Direct Invoice</h1>
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        Admin Only
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Issue an official invoice directly without a prior Job Card or Quotation.</p>
            </div>
        </div>

        <div class="text-right">
            <span class="text-xs text-gray-400 block">Suggested Invoice #</span>
            <span class="text-sm font-bold text-[#0B1849]">{{ $suggestedNumber }}</span>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <div class="font-bold flex items-center">
                <svg class="w-4 h-4 mr-1.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Please correct the following errors:
            </div>
            <ul class="list-disc list-inside text-xs pl-2 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('operations.invoices.store') }}" class="space-y-6">
        @csrf

        <!-- Customer Selection Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-2">
                Customer & Invoice Terms
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Customer Picker -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Customer <span class="text-rose-500">*</span>
                    </label>
                    <x-searchable-select 
                        name="customer_id" 
                        :value="old('customer_id', $selectedCustomerId ?? '')"
                        :endpoint="route('search.customers')"
                        :required="true"
                        placeholder="Search Customer (Name, Mobile, Company)..."
                        @selected="selectedCustomerId = $event.detail.id; onCustomerChange()"
                        @cleared="selectedCustomerId = ''; onCustomerChange()"
                    />
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Invoice Subject / Description
                    </label>
                    <input 
                        type="text" 
                        name="subject" 
                        value="{{ old('subject') }}"
                        placeholder="e.g. Corporate Fleet Fleet Maintenance Service"
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] min-h-[44px]"
                    />
                </div>
            </div>

            <!-- Dates & Reference -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Invoice Date <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="invoice_date" 
                        value="{{ old('invoice_date', now()->toDateString()) }}"
                        required
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] min-h-[44px]"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Payment Due Date
                    </label>
                    <input 
                        type="date" 
                        name="due_date" 
                        value="{{ old('due_date', now()->addDays(7)->toDateString()) }}"
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] min-h-[44px]"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Client PO / Reference #
                    </label>
                    <input 
                        type="text" 
                        name="reference" 
                        value="{{ old('reference') }}"
                        placeholder="e.g. PO-88912"
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] min-h-[44px]"
                    />
                </div>
            </div>
        </div>

        <!-- Line Items Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Invoice Items</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Add labor services, spare parts, or manual custom items.</p>
                </div>

                <!-- Add Item Dropdown Buttons -->
                <div class="flex items-center space-x-2">
                    <button 
                        type="button" 
                        @click="addItem('service')"
                        class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs rounded-lg transition flex items-center cursor-pointer min-h-[36px]"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Add Service
                    </button>
                    <button 
                        type="button" 
                        @click="addItem('product')"
                        class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-lg transition flex items-center cursor-pointer min-h-[36px]"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Add Product
                    </button>
                    <button 
                        type="button" 
                        @click="addItem('custom')"
                        class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold text-xs rounded-lg transition flex items-center cursor-pointer min-h-[36px]"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Custom Line
                    </button>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50/75 text-gray-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-2.5 px-2 w-10 text-center">#</th>
                            <th class="py-2.5 px-3 min-w-[200px]">Item / Description</th>
                            <th class="py-2.5 px-3 w-24">Type</th>
                            <th class="py-2.5 px-3 w-28 text-right">Unit Price</th>
                            <th class="py-2.5 px-3 w-20 text-center">Qty</th>
                            <th class="py-2.5 px-3 w-32 text-right">Line Disc</th>
                            <th class="py-2.5 px-3 w-32 text-right">Total (LKR)</th>
                            <th class="py-2.5 px-2 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-2 text-center text-gray-400 font-bold" x-text="index + 1"></td>
                                
                                <td class="py-3 px-3 space-y-1.5">
                                    <!-- Service Select -->
                                    <template x-if="item.item_type === 'service'">
                                        <select 
                                            class="block w-full px-2.5 py-1.5 text-xs text-gray-900 border border-gray-300 rounded focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[36px]"
                                            @change="onSelectItem(index, $event.target.value, 'service')"
                                            x-model="item.item_id"
                                        >
                                            <option value="">-- Choose Existing Service --</option>
                                            @foreach($services as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }} (LKR {{ number_format($s->default_price, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </template>

                                    <!-- Product Select -->
                                    <template x-if="item.item_type === 'product'">
                                        <select 
                                            class="block w-full px-2.5 py-1.5 text-xs text-gray-900 border border-gray-300 rounded focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[36px]"
                                            @change="onSelectItem(index, $event.target.value, 'product')"
                                            x-model="item.item_id"
                                        >
                                            <option value="">-- Choose Existing Product --</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->name }} [{{ $p->sku }}] 
                                                    (LKR {{ number_format($p->selling_price, 2) }} | Avail: @quantity($p->available_stock))
                                                </option>
                                            @endforeach
                                        </select>
                                    </template>

                                    <!-- Item Name Input -->
                                    <input 
                                        type="text" 
                                        :name="'items[' + index + '][item_name]'" 
                                        x-model="item.item_name"
                                        placeholder="Item name / title *"
                                        required
                                        class="block w-full px-2.5 py-1.5 text-xs font-semibold text-gray-900 border border-gray-300 rounded focus:ring-1 focus:ring-[#0B1849] min-h-[34px]"
                                    >

                                    <!-- Description Input -->
                                    <input 
                                        type="text" 
                                        :name="'items[' + index + '][description]'" 
                                        x-model="item.description"
                                        placeholder="Optional line description or notes..."
                                        class="block w-full px-2.5 py-1 text-[11px] text-gray-600 border border-gray-200 rounded focus:ring-1 focus:ring-[#0B1849] min-h-[30px]"
                                    >

                                    <input type="hidden" :name="'items[' + index + '][item_type]'" :value="item.item_type">
                                    <input type="hidden" :name="'items[' + index + '][item_id]'" :value="item.item_id">
                                    <input type="hidden" :name="'items[' + index + '][item_code]'" :value="item.item_code">
                                    <input type="hidden" :name="'items[' + index + '][unit]'" :value="item.unit">
                                </td>

                                <td class="py-3 px-3">
                                    <span 
                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                        :class="{
                                            'bg-blue-100 text-blue-800': item.item_type === 'service',
                                            'bg-emerald-100 text-emerald-800': item.item_type === 'product',
                                            'bg-gray-100 text-gray-700': item.item_type === 'custom'
                                        }"
                                        x-text="item.item_type"
                                    ></span>
                                </td>

                                <td class="py-3 px-3 text-right">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0"
                                        :name="'items[' + index + '][unit_price]'" 
                                        x-model="item.unit_price"
                                        required
                                        class="block w-full px-2 py-1.5 text-xs text-right text-gray-900 border border-gray-300 rounded focus:ring-1 focus:ring-[#0B1849] min-h-[34px]"
                                    >
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0.01"
                                        :name="'items[' + index + '][quantity]'" 
                                        x-model="item.quantity"
                                        required
                                        class="block w-full px-2 py-1.5 text-xs text-center text-gray-900 border border-gray-300 rounded focus:ring-1 focus:ring-[#0B1849] min-h-[34px]"
                                    >
                                </td>

                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center space-x-1">
                                        <select 
                                            :name="'items[' + index + '][discount_type]'" 
                                            x-model="item.discount_type"
                                            class="w-16 px-1 py-1 text-[11px] border border-gray-300 rounded bg-white min-h-[34px]"
                                        >
                                            <option value="none">None</option>
                                            <option value="percentage">%</option>
                                            <option value="fixed">LKR</option>
                                        </select>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0"
                                            :name="'items[' + index + '][discount_value]'" 
                                            x-model="item.discount_value"
                                            :disabled="item.discount_type === 'none'"
                                            class="w-16 px-1.5 py-1 text-xs text-right text-gray-900 border border-gray-300 rounded disabled:bg-gray-100 min-h-[34px]"
                                        >
                                    </div>
                                </td>

                                <td class="py-3 px-3 text-right font-bold text-gray-900 whitespace-nowrap">
                                    <span x-text="'LKR ' + formatMoney(calculateLineTotal(item))"></span>
                                </td>

                                <td class="py-3 px-2 text-center">
                                    <button 
                                        type="button" 
                                        @click="removeItem(index)" 
                                        class="text-gray-400 hover:text-rose-600 transition p-1"
                                        title="Remove item"
                                    >
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Document Discounts, Taxes, and Totals -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left: Notes & Terms -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Notes & Terms</h3>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Customer Notes / Memo
                    </label>
                    <textarea 
                        name="notes" 
                        rows="3" 
                        placeholder="Add special instructions or thank you notes visible on invoice..."
                        class="block w-full px-3 py-2 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849]"
                    >{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Terms & Conditions
                    </label>
                    <textarea 
                        name="terms_conditions" 
                        rows="3" 
                        placeholder="e.g. Payment due within 7 days. Cheques drawn in favor of AutoCare..."
                        class="block w-full px-3 py-2 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849]"
                    >{{ old('terms_conditions', '1. Goods and services sold are subject to AutoCare warranty terms.\n2. Payment is due upon receipt unless credit terms agreed.') }}</textarea>
                </div>
            </div>

            <!-- Right: Calculations & Grand Total -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Financial Summary</h3>

                <div class="space-y-3 text-xs sm:text-sm">
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Items Subtotal:</span>
                        <span class="font-bold text-gray-900" x-text="'LKR ' + formatMoney(calculateSubtotal())"></span>
                    </div>

                    <!-- Document Discount -->
                    <div class="flex items-center justify-between text-gray-600 pt-1">
                        <span class="text-xs">Document Discount:</span>
                        <div class="flex items-center space-x-1">
                            <select 
                                name="discount_type" 
                                x-model="discountType"
                                class="px-2 py-1 text-xs border border-gray-300 rounded bg-white min-h-[32px]"
                            >
                                <option value="none">No Discount</option>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed (LKR)</option>
                            </select>
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                name="discount_value" 
                                x-model="discountValue"
                                :disabled="discountType === 'none'"
                                placeholder="0.00"
                                class="w-20 px-2 py-1 text-xs text-right border border-gray-300 rounded disabled:bg-gray-100 min-h-[32px]"
                            >
                        </div>
                    </div>
                    <template x-if="calculateDocDiscount() > 0">
                        <div class="flex justify-between text-emerald-700 text-xs">
                            <span>Discount Amount:</span>
                            <span class="font-bold" x-text="'- LKR ' + formatMoney(calculateDocDiscount())"></span>
                        </div>
                    </template>

                    <!-- Tax / VAT -->
                    <div class="flex items-center justify-between text-gray-600 pt-1">
                        <span class="text-xs">Tax / VAT (%):</span>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            max="100" 
                            name="tax_percentage" 
                            x-model="taxPercentage"
                            placeholder="0"
                            class="w-24 px-2 py-1 text-xs text-right border border-gray-300 rounded min-h-[32px]"
                        >
                    </div>
                    <template x-if="calculateTaxAmount() > 0">
                        <div class="flex justify-between text-gray-700 text-xs">
                            <span>Tax Amount:</span>
                            <span class="font-bold" x-text="'LKR ' + formatMoney(calculateTaxAmount())"></span>
                        </div>
                    </template>

                    <!-- Additional Charges -->
                    <div class="flex items-center justify-between text-gray-600 pt-1">
                        <span class="text-xs">Additional Charges (LKR):</span>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            name="additional_charges" 
                            x-model="additionalCharges"
                            placeholder="0.00"
                            class="w-24 px-2 py-1 text-xs text-right border border-gray-300 rounded min-h-[32px]"
                        >
                    </div>

                    <!-- Grand Total Banner -->
                    <div class="pt-3 border-t border-gray-200 flex justify-between items-center text-base sm:text-lg font-black text-[#0B1849]">
                        <span>Grand Total:</span>
                        <span x-text="'LKR ' + formatMoney(calculateGrandTotal())"></span>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('operations.invoices.index') }}" 
                        class="px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 rounded-lg transition min-h-[44px] flex items-center justify-center"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition min-h-[44px] cursor-pointer"
                    >
                        Create & Issue Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function invoiceForm(config) {
    return {
        services: config.services || [],
        products: config.products || [],
        vehicles: config.vehicles || [],
        customers: config.customers || [],
        selectedCustomerId: config.selectedCustomerId || '',
        
        discountType: '{{ old('discount_type', 'none') }}',
        discountValue: '{{ old('discount_value', 0) }}',
        taxPercentage: '{{ old('tax_percentage', 0) }}',
        additionalCharges: '{{ old('additional_charges', 0) }}',

        items: [],

        init() {
            if (config.oldItems && config.oldItems.length > 0) {
                this.items = config.oldItems.map(item => ({
                    item_type: item.item_type || 'custom',
                    item_id: item.item_id || null,
                    item_code: item.item_code || '',
                    item_name: item.item_name || '',
                    description: item.description || '',
                    unit: item.unit || 'Unit',
                    unit_price: parseFloat(item.unit_price) || 0,
                    quantity: parseFloat(item.quantity) || 1,
                    discount_type: item.discount_type || 'none',
                    discount_value: parseFloat(item.discount_value) || 0,
                }));
            } else {
                // Initial blank line item
                this.addItem('service');
            }
        },

        onCustomerChange() {
            // Customer changed
        },

        addItem(type) {
            this.items.push({
                item_type: type,
                item_id: null,
                item_code: '',
                item_name: '',
                description: '',
                unit: type === 'service' ? 'Job' : 'Unit',
                unit_price: 0.00,
                quantity: 1,
                discount_type: 'none',
                discount_value: 0
            });
        },

        removeItem(index) {
            if (this.items.length <= 1) {
                alert('An invoice must have at least one line item.');
                return;
            }
            this.items.splice(index, 1);
        },

        onSelectItem(index, id, type) {
            if (!id) return;
            const item = this.items[index];
            if (type === 'service') {
                const s = this.services.find(x => x.id == id);
                if (s) {
                    item.item_id = s.id;
                    item.item_code = s.code;
                    item.item_name = s.name;
                    item.description = s.description || '';
                    item.unit = 'Job';
                    item.unit_price = parseFloat(s.default_price || 0);
                }
            } else if (type === 'product') {
                const p = this.products.find(x => x.id == id);
                if (p) {
                    item.item_id = p.id;
                    item.item_code = p.sku;
                    item.item_name = p.name;
                    item.description = p.description || '';
                    item.unit = p.uom?.abbreviation || 'Unit';
                    item.unit_price = parseFloat(p.selling_price || 0);
                }
            }
        },

        calculateLineTotal(item) {
            const qty = parseFloat(item.quantity) || 0;
            const price = parseFloat(item.unit_price) || 0;
            const lineSubtotal = qty * price;

            let discount = 0;
            if (item.discount_type === 'percentage') {
                discount = lineSubtotal * ((parseFloat(item.discount_value) || 0) / 100);
            } else if (item.discount_type === 'fixed') {
                discount = parseFloat(item.discount_value) || 0;
            }

            return Math.max(0, lineSubtotal - discount);
        },

        calculateSubtotal() {
            return this.items.reduce((sum, item) => sum + this.calculateLineTotal(item), 0);
        },

        calculateDocDiscount() {
            const subtotal = this.calculateSubtotal();
            if (this.discountType === 'percentage') {
                return subtotal * ((parseFloat(this.discountValue) || 0) / 100);
            } else if (this.discountType === 'fixed') {
                return Math.min(subtotal, parseFloat(this.discountValue) || 0);
            }
            return 0;
        },

        calculateTaxAmount() {
            const taxable = Math.max(0, this.calculateSubtotal() - this.calculateDocDiscount());
            const taxPct = parseFloat(this.taxPercentage) || 0;
            return taxPct > 0 ? (taxable * (taxPct / 100)) : 0;
        },

        calculateGrandTotal() {
            const taxable = Math.max(0, this.calculateSubtotal() - this.calculateDocDiscount());
            const tax = this.calculateTaxAmount();
            const additional = parseFloat(this.additionalCharges) || 0;
            return Math.max(0, taxable + tax + additional);
        },

        formatMoney(amount) {
            return parseFloat(amount || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };
}
</script>
@endpush
@endsection
