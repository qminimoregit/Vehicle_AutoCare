@extends('layouts.app')

@section('title', 'New Quotation')

@section('content')
<div 
    x-data="quotationForm({
        services: {{ Js::from($services) }},
        products: {{ Js::from($products) }},
        vehicles: {{ Js::from($vehicles) }},
        customers: {{ Js::from($customers) }},
        selectedCustomerId: '{{ old('customer_id', $selectedCustomerId ?? '') }}',
        selectedVehicleId: '{{ old('vehicle_id', $selectedVehicleId ?? '') }}',
        oldItems: {{ Js::from(old('items', [])) }}
    })"
    class="max-w-5xl mx-auto space-y-6"
>
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('billing.quotations.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">Create Quotation</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Prepare an official price estimate for service or vehicle spare parts.</p>
            </div>
        </div>

        <div class="text-right">
            <span class="text-xs text-gray-400 block">Suggested Reference</span>
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

    <form method="POST" action="{{ route('billing.quotations.store') }}" class="space-y-6">
        @csrf

        <!-- Customer & Vehicle Selection Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-5">
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-2">
                Customer & Vehicle Details
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
                        placeholder="Search customer by name or phone..."
                        @selected="selectedCustomerId = $event.detail.id; onCustomerChange()"
                        @cleared="selectedCustomerId = ''; onCustomerChange()"
                    />
                </div>

                <!-- Vehicle Picker (Optional) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Vehicle (Optional)
                    </label>
                    <select 
                        name="vehicle_id" 
                        x-model="selectedVehicleId"
                        class="block w-full px-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                    >
                        <option value="">-- No Vehicle / General Quotation --</option>
                        <template x-for="v in filteredVehicles" :key="v.id">
                            <option :value="v.id" x-text="v.vehicle_number + (v.make_model ? ' (' + v.make_model + ')' : '')"></option>
                        </template>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">Leave blank if quoting general parts or non-vehicle client sales.</p>
                </div>
            </div>

            <!-- Quotation Dates & Subject -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Quotation Date <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="quotation_date" 
                        value="{{ old('quotation_date', now()->toDateString()) }}"
                        required
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Valid Until (Expiry)
                    </label>
                    <input 
                        type="date" 
                        name="expiry_date" 
                        value="{{ old('expiry_date', now()->addDays(14)->toDateString()) }}"
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Subject / Title
                    </label>
                    <input 
                        type="text" 
                        name="subject" 
                        value="{{ old('subject') }}"
                        placeholder="e.g. Periodic Full Service & Brake Overhaul"
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Customer Reference #
                    </label>
                    <input 
                        type="text" 
                        name="reference" 
                        value="{{ old('reference') }}"
                        placeholder="e.g. PO-8921 / Request Ref"
                        class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                    />
                </div>
            </div>
        </div>

        <!-- Line Items Section -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">
                    Services & Products Line Items
                </h2>
                <div class="flex items-center space-x-2">
                    <button 
                        type="button" 
                        @click="addItem('service')" 
                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-bold rounded-lg transition cursor-pointer"
                    >
                        + Add Service
                    </button>
                    <button 
                        type="button" 
                        @click="addItem('product')" 
                        class="inline-flex items-center px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold rounded-lg transition cursor-pointer"
                    >
                        + Add Product
                    </button>
                    <button 
                        type="button" 
                        @click="addItem('custom')" 
                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold rounded-lg transition cursor-pointer"
                    >
                        + Custom Item
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="text-[11px] font-bold text-gray-500 uppercase tracking-wider bg-gray-50/50">
                            <th class="py-2.5 px-3 text-left w-12">#</th>
                            <th class="py-2.5 px-3 text-left">Type & Item</th>
                            <th class="py-2.5 px-3 text-left w-44">Description / Notes</th>
                            <th class="py-2.5 px-3 text-center w-24">Qty</th>
                            <th class="py-2.5 px-3 text-right w-32">Unit Price (LKR)</th>
                            <th class="py-2.5 px-3 text-right w-28">Discount</th>
                            <th class="py-2.5 px-3 text-right w-36">Total (LKR)</th>
                            <th class="py-2.5 px-3 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-3 text-xs font-bold text-gray-400" x-text="index + 1"></td>
                                
                                <td class="py-3 px-3">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center space-x-2">
                                            <span 
                                                class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                                :class="{
                                                    'bg-blue-100 text-blue-800': item.item_type === 'service',
                                                    'bg-emerald-100 text-emerald-800': item.item_type === 'product',
                                                    'bg-gray-100 text-gray-700': item.item_type === 'custom'
                                                }"
                                                x-text="item.item_type"
                                            ></span>

                                            <input type="hidden" :name="'items[' + index + '][item_type]'" :value="item.item_type">
                                            <input type="hidden" :name="'items[' + index + '][item_id]'" :value="item.item_id">
                                            <input type="hidden" :name="'items[' + index + '][item_code]'" :value="item.item_code">

                                            <!-- Pre-defined selection -->
                                            <template x-if="item.item_type === 'service'">
                                                <select 
                                                    class="block w-full px-2.5 py-1.5 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] bg-white"
                                                    @change="onSelectItem(index, $event.target.value, 'service')"
                                                >
                                                    <option value="">-- Pick Master Service --</option>
                                                    <template x-for="s in services" :key="s.id">
                                                        <option :value="s.id" :selected="item.item_id == s.id" x-text="s.name + ' (' + s.code + ')'"></option>
                                                    </template>
                                                </select>
                                            </template>

                                            <template x-if="item.item_type === 'product'">
                                                <select 
                                                    class="block w-full px-2.5 py-1.5 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] bg-white"
                                                    @change="onSelectItem(index, $event.target.value, 'product')"
                                                >
                                                    <option value="">-- Pick Product/Part --</option>
                                                    <template x-for="p in products" :key="p.id">
                                                        <option :value="p.id" :selected="item.item_id == p.id" x-text="p.name + ' (Stock: ' + p.available_stock + ')'"></option>
                                                    </template>
                                                </select>
                                            </template>
                                        </div>

                                        <input 
                                            type="text" 
                                            :name="'items[' + index + '][item_name]'"
                                            x-model="item.item_name"
                                            required
                                            placeholder="Item Name"
                                            class="block w-full px-2.5 py-1.5 text-xs font-semibold text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849]"
                                        />
                                    </div>
                                </td>

                                <td class="py-3 px-3">
                                    <input 
                                        type="text" 
                                        :name="'items[' + index + '][description]'"
                                        x-model="item.description"
                                        placeholder="Optional description / specs"
                                        class="block w-full px-2.5 py-1.5 text-xs text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849]"
                                    />
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center space-x-1">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0.01" 
                                            :name="'items[' + index + '][quantity]'"
                                            x-model.number="item.quantity"
                                            required
                                            class="block w-16 px-2 py-1.5 text-xs text-center text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849]"
                                        />
                                        <input 
                                            type="hidden" 
                                            :name="'items[' + index + '][unit]'"
                                            :value="item.unit || 'Unit'"
                                        />
                                    </div>
                                </td>

                                <td class="py-3 px-3 text-right">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0" 
                                        :name="'items[' + index + '][unit_price]'"
                                        x-model.number="item.unit_price"
                                        required
                                        class="block w-28 px-2 py-1.5 text-xs text-right text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] ml-auto"
                                    />
                                </td>

                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center space-x-1 justify-end">
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0" 
                                            :name="'items[' + index + '][discount_value]'"
                                            x-model.number="item.discount_value"
                                            class="block w-16 px-2 py-1.5 text-xs text-right text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849]"
                                        />
                                        <select 
                                            :name="'items[' + index + '][discount_type]'"
                                            x-model="item.discount_type"
                                            class="text-[11px] border border-gray-300 rounded px-1 py-1.5 bg-white"
                                        >
                                            <option value="none">-</option>
                                            <option value="fixed">LKR</option>
                                            <option value="percentage">%</option>
                                        </select>
                                    </div>
                                </td>

                                <td class="py-3 px-3 text-right">
                                    <span class="text-xs font-bold text-gray-900" x-text="formatMoney(calculateLineTotal(item))"></span>
                                </td>

                                <td class="py-3 px-3 text-center">
                                    <button 
                                        type="button" 
                                        @click="removeItem(index)" 
                                        class="text-gray-400 hover:text-rose-600 transition p-1"
                                        title="Remove row"
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

        <!-- Totals & Notes Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Notes & Terms -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400">Notes & Terms</h2>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Client Notes (Printed on Quotation)
                    </label>
                    <textarea 
                        name="notes" 
                        rows="3" 
                        placeholder="e.g. Free vehicle inspection included. Estimates subject to dismantling discovery."
                        class="block w-full px-3 py-2 text-xs text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                    >{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Terms & Conditions
                    </label>
                    <textarea 
                        name="terms_conditions" 
                        rows="3" 
                        class="block w-full px-3 py-2 text-xs text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                    >{{ old('terms_conditions', 'Quotation valid for 14 days. Parts subject to supplier market availability. Service labor warranty 30 days.') }}</textarea>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-3">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-2">
                    Calculation Summary
                </h2>

                <div class="flex justify-between items-center text-sm py-1">
                    <span class="text-gray-500 font-medium">Subtotal</span>
                    <span class="font-bold text-gray-900" x-text="formatMoney(calculateSubtotal())"></span>
                </div>

                <!-- Document Discount -->
                <div class="flex justify-between items-center text-sm py-1">
                    <div class="flex items-center space-x-1">
                        <span class="text-gray-500 font-medium">Discount</span>
                        <select 
                            name="discount_type" 
                            x-model="discountType"
                            class="text-[11px] border border-gray-300 rounded px-1.5 py-0.5 bg-white"
                        >
                            <option value="none">None</option>
                            <option value="fixed">Fixed (LKR)</option>
                            <option value="percentage">Percentage (%)</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-1 justify-end">
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            name="discount_value" 
                            x-model.number="discountValue"
                            :disabled="discountType === 'none'"
                            class="w-24 px-2 py-1 text-xs text-right border border-gray-300 rounded-lg"
                        />
                    </div>
                </div>

                <!-- Tax / VAT Percentage -->
                <div class="flex justify-between items-center text-sm py-1">
                    <div class="flex items-center space-x-1">
                        <span class="text-gray-500 font-medium">Tax / VAT (%)</span>
                    </div>
                    <div class="flex items-center space-x-1 justify-end">
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0" 
                            max="100" 
                            name="tax_percentage" 
                            x-model.number="taxPercentage"
                            placeholder="0.00"
                            class="w-20 px-2 py-1 text-xs text-right border border-gray-300 rounded-lg"
                        />
                        <span class="text-xs text-gray-400 w-16 text-right" x-text="'+ ' + formatMoney(calculateTaxAmount())"></span>
                    </div>
                </div>

                <!-- Additional Charges -->
                <div class="flex justify-between items-center text-sm py-1">
                    <span class="text-gray-500 font-medium">Additional Charges</span>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="0" 
                        name="additional_charges" 
                        x-model.number="additionalCharges"
                        class="w-24 px-2 py-1 text-xs text-right border border-gray-300 rounded-lg"
                    />
                </div>

                <!-- Grand Total -->
                <div class="border-t-2 border-gray-900 pt-3 flex justify-between items-baseline">
                    <span class="text-base font-black text-gray-900">Grand Total</span>
                    <div class="text-right">
                        <span class="text-xs text-gray-400 mr-1">LKR</span>
                        <span class="text-2xl font-black text-[#0B1849]" x-text="formatMoney(calculateGrandTotal())"></span>
                    </div>
                </div>

                <!-- Form Submit Action -->
                <div class="pt-4">
                    <button 
                        type="submit" 
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-xs transition cursor-pointer min-h-[46px]"
                    >
                        Save & Preview Quotation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function quotationForm(config) {
    return {
        services: config.services || [],
        products: config.products || [],
        vehicles: config.vehicles || [],
        customers: config.customers || [],
        selectedCustomerId: config.selectedCustomerId || '',
        selectedVehicleId: config.selectedVehicleId || '',
        discountType: 'none',
        discountValue: 0,
        taxPercentage: 0,
        additionalCharges: 0,
        items: [],

        init() {
            if (config.oldItems && config.oldItems.length > 0) {
                this.items = config.oldItems.map(item => ({
                    item_type: item.item_type || 'service',
                    item_id: item.item_id || null,
                    item_code: item.item_code || '',
                    item_name: item.item_name || '',
                    description: item.description || '',
                    quantity: parseFloat(item.quantity) || 1,
                    unit: item.unit || 'Unit',
                    unit_price: parseFloat(item.unit_price) || 0,
                    discount_type: item.discount_type || 'none',
                    discount_value: parseFloat(item.discount_value) || 0,
                }));
            } else {
                // Initial default row
                this.addItem('service');
            }
        },

        get filteredVehicles() {
            if (!this.selectedCustomerId) {
                return this.vehicles;
            }
            return this.vehicles.filter(v => v.customer_id == this.selectedCustomerId);
        },

        onCustomerChange() {
            // Auto pick vehicle if customer has exactly one vehicle
            const customerVehicles = this.vehicles.filter(v => v.customer_id == this.selectedCustomerId);
            if (customerVehicles.length === 1) {
                this.selectedVehicleId = customerVehicles[0].id;
            } else {
                this.selectedVehicleId = '';
            }
        },

        addItem(type = 'service') {
            this.items.push({
                item_type: type,
                item_id: null,
                item_code: '',
                item_name: '',
                description: '',
                quantity: 1,
                unit: type === 'service' ? 'Job' : 'Pcs',
                unit_price: 0,
                discount_type: 'none',
                discount_value: 0,
            });
        },

        removeItem(index) {
            if (this.items.length <= 1) {
                alert('At least one item row is required.');
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
                    // Check if service vehicle pricing exists or default
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
