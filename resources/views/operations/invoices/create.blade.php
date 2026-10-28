@extends('layouts.app')

@section('title', 'New Direct Invoice')

@section('content')
<div 
    x-data="invoiceForm({
        services: {{ Js::from($services) }},
        products: {{ Js::from($products) }},
        vehicles: {{ Js::from($vehicles) }},
        vehicleTypes: {{ Js::from($vehicleTypes) }},
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

    <form method="POST" action="{{ route('operations.invoices.store') }}" class="space-y-6">
        @csrf

        <!-- Customer & Vehicle Selection Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-xs space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-gray-100 pb-3 gap-2">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500">
                        Customer &amp; Vehicle Details
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">Search an existing vehicle or customer, or add a new customer and vehicle.</p>
                </div>

                <!-- + Add New Customer & Vehicle Button -->
                <button 
                    type="button" 
                    @click="openNewCustomerModal()"
                    class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-[#0B1849] bg-[#0B1849]/5 hover:bg-[#0B1849]/10 border border-[#0B1849]/20 rounded-lg transition cursor-pointer shadow-2xs shrink-0"
                >
                    <svg class="w-4 h-4 mr-1.5 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Customer &amp; Vehicle
                </button>
            </div>

            <!-- Hidden Inputs to submit with form -->
            <input type="hidden" name="customer_id" id="hidden_customer_id" :value="selectedCustomerId" x-model="selectedCustomerId">
            <input type="hidden" name="vehicle_id" id="hidden_vehicle_id" :value="selectedVehicleId" x-model="selectedVehicleId">
            <input type="hidden" name="new_customer_name" :value="newCustomerName">
            <input type="hidden" name="new_customer_mobile" :value="newCustomerMobile">
            <input type="hidden" name="new_customer_email" :value="newCustomerEmail">
            <input type="hidden" name="new_vehicle_number" :value="newVehicleNumber">
            <input type="hidden" name="new_vehicle_type_id" :value="newVehicleTypeId">

            <!-- Active / Selected Customer & Vehicle Summary Card -->
            <div x-show="selectedCustomerName" x-cloak class="p-4 bg-gray-50/80 rounded-xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start sm:items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-xl bg-[#0B1849] text-white flex items-center justify-center font-bold text-base shrink-0 shadow-2xs">
                        <template x-if="selectedVehicleNumber">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                        </template>
                        <template x-if="!selectedVehicleNumber">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </template>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900 flex items-center gap-2 flex-wrap">
                            <span>Customer: <strong x-text="selectedCustomerName"></strong></span>
                            <span x-show="selectedCustomerPhone" class="text-xs text-gray-500 font-medium" x-text="'(' + selectedCustomerPhone + ')'"></span>
                            <span x-show="isNewCustomer" class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 rounded-full border border-emerald-200">New Registration</span>
                            <span x-show="!isNewCustomer" class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 rounded-full border border-blue-200">Registered</span>
                        </div>
                        <div class="text-xs text-gray-600 mt-1 flex items-center gap-1.5 flex-wrap">
                            <span class="font-semibold text-gray-500 uppercase tracking-wider text-[11px]">Vehicle:</span>
                            <template x-if="selectedVehicleNumber">
                                <span class="font-mono font-bold text-gray-800 bg-white px-2 py-0.5 rounded border border-gray-200" x-text="selectedVehicleNumber"></span>
                            </template>
                            <span x-show="selectedVehicleType" class="text-gray-500" x-text="'(' + selectedVehicleType + ')'"></span>
                            <span x-show="!selectedVehicleNumber" class="text-gray-400 italic">No vehicle specified</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <button 
                        type="button" 
                        @click="clearCustomerAndVehicle()" 
                        class="px-3.5 py-1.5 text-xs font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-200/70 bg-white border border-gray-300 rounded-lg shadow-2xs transition cursor-pointer flex items-center"
                    >
                        <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Change
                    </button>
                </div>
            </div>

            <!-- Unified Search Field (shown when nothing selected) -->
            <div
                x-show="!selectedCustomerName"
                class="space-y-1.5"
                x-on:selected="handleEntitySelect($event.detail)"
                x-on:cleared="clearCustomerAndVehicle()"
            >
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                    SEARCH VEHICLE OR CUSTOMER
                </label>
                <x-searchable-select 
                    name="customer_search" 
                    :endpoint="route('search.customers-and-vehicles')"
                    placeholder="Search vehicle number or customer name (optional)..."
                    search-placeholder="Search vehicle number or customer name (optional)..."
                    :required="false"
                    @selected="handleEntitySelect($event.detail)"
                    @cleared="clearCustomerAndVehicle()"
                />
                <p class="text-[11px] text-gray-400">Search by vehicle number, customer name or mobile number.</p>
            </div>

            <!-- Dates & Reference & Subject -->
            <div class="border-t border-gray-100 pt-4 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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

                <!-- Subject -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Invoice Subject / Description
                    </label>
                    <input 
                        type="text" 
                        name="subject" 
                        value="{{ old('subject') }}"
                        placeholder="e.g. Corporate Fleet Maintenance Service"
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

                <!-- Add Item Buttons: Exactly ONE visible plus icon each -->
                <div class="flex items-center space-x-2">
                    <button 
                        type="button" 
                        @click="addItem('service')"
                        class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs rounded-lg transition flex items-center cursor-pointer min-h-[36px]"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Service
                    </button>
                    <button 
                        type="button" 
                        @click="addItem('product')"
                        class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-bold text-xs rounded-lg transition flex items-center cursor-pointer min-h-[36px]"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product
                    </button>
                    <button 
                        type="button" 
                        @click="addItem('custom')"
                        class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold text-xs rounded-lg transition flex items-center cursor-pointer min-h-[36px]"
                    >
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Custom Line
                    </button>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto min-h-[320px] pb-12">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50/75 text-gray-500 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-2.5 px-2 w-10 text-center">#</th>
                            <th class="py-2.5 px-3 min-w-[280px]">Item / Description</th>
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
                                    <input type="hidden" :name="'items[' + index + '][item_type]'" :value="item.item_type">
                                    <input type="hidden" :name="'items[' + index + '][item_id]'" :value="item.item_id">
                                    <input type="hidden" :name="'items[' + index + '][item_code]'" :value="item.item_code">
                                    <input type="hidden" :name="'items[' + index + '][unit]'" :value="item.unit">

                                    <!-- Searchable Service Selector -->
                                    <template x-if="item.item_type === 'service'">
                                        <div class="relative w-full" x-data="{
                                            openServiceSearch: false,
                                            searchQuery: item.item_code ? `${item.item_name} (${item.item_code})` : item.item_name,
                                            highlightedIndex: 0,
                                            get filteredServices() {
                                                const q = (this.searchQuery || '').trim().toLowerCase();
                                                if (!q) return services.slice(0, 20);
                                                return services.filter(s => {
                                                    const name = (s.name || '').toLowerCase();
                                                    const code = (s.code || '').toLowerCase();
                                                    return name.includes(q) || code.includes(q);
                                                }).slice(0, 20);
                                            },
                                            selectService(s) {
                                                onSelectItem(index, s.id, 'service');
                                                this.searchQuery = s.name + (s.code ? ' (' + s.code + ')' : '');
                                                this.openServiceSearch = false;
                                            },
                                            clear() {
                                                item.item_id = null;
                                                item.item_code = '';
                                                item.item_name = '';
                                                item.description = '';
                                                item.unit_price = 0;
                                                this.searchQuery = '';
                                                this.openServiceSearch = false;
                                                calculateLineTotal(item);
                                            },
                                            onKeydown(e) {
                                                if (!this.openServiceSearch) {
                                                    if (e.key === 'ArrowDown' || e.key === 'Enter') {
                                                        this.openServiceSearch = true;
                                                        e.preventDefault();
                                                    }
                                                    return;
                                                }
                                                if (e.key === 'ArrowDown') {
                                                    e.preventDefault();
                                                    const count = this.filteredServices.length;
                                                    if (count > 0) this.highlightedIndex = (this.highlightedIndex + 1) % count;
                                                } else if (e.key === 'ArrowUp') {
                                                    e.preventDefault();
                                                    const count = this.filteredServices.length;
                                                    if (count > 0) this.highlightedIndex = (this.highlightedIndex - 1 + count) % count;
                                                } else if (e.key === 'Enter') {
                                                    e.preventDefault();
                                                    const sList = this.filteredServices;
                                                    if (sList.length > 0 && this.highlightedIndex >= 0 && this.highlightedIndex < sList.length) {
                                                        this.selectService(sList[this.highlightedIndex]);
                                                    }
                                                } else if (e.key === 'Escape') {
                                                    this.openServiceSearch = false;
                                                }
                                            }
                                        }" @click.outside="openServiceSearch = false">
                                            <div class="relative">
                                                <input 
                                                    type="text" 
                                                    x-model="searchQuery"
                                                    @focus="openServiceSearch = true"
                                                    @input="openServiceSearch = true; highlightedIndex = 0"
                                                    @keydown="onKeydown($event)"
                                                    placeholder="Search service name or code..."
                                                    class="block w-full pr-7 pl-2.5 py-1.5 text-xs text-gray-900 border border-gray-300 rounded focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[34px]"
                                                    autocomplete="off"
                                                />
                                                <button 
                                                    type="button" 
                                                    x-show="searchQuery.length > 0" 
                                                    @click="clear()" 
                                                    class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer"
                                                    title="Clear selection"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div 
                                                x-show="openServiceSearch" 
                                                x-transition
                                                class="absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded shadow-lg max-h-52 overflow-y-auto min-w-[260px]"
                                                style="display: none;"
                                            >
                                                <template x-for="(s, sIdx) in filteredServices" :key="s.id">
                                                    <div 
                                                        @click="selectService(s)"
                                                        @mouseenter="highlightedIndex = sIdx"
                                                        :class="{'bg-[#0B1849]/10 text-[#0B1849]': highlightedIndex === sIdx, 'text-gray-700': highlightedIndex !== sIdx}"
                                                        class="px-3 py-2 cursor-pointer border-b border-gray-50 last:border-b-0 text-left transition"
                                                    >
                                                        <div class="font-semibold text-xs text-gray-900" x-text="s.name"></div>
                                                        <div class="text-[11px] text-gray-500 font-mono mt-0.5 flex items-center justify-between">
                                                            <span x-text="'Code: ' + (s.code || 'N/A')"></span>
                                                            <span x-show="s.default_price" x-text="'LKR ' + parseFloat(s.default_price).toFixed(2)"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="filteredServices.length === 0" class="px-3 py-2.5 text-xs text-gray-400 text-center">
                                                    No services found matching "<span class="font-semibold" x-text="searchQuery"></span>"
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Searchable Product Selector -->
                                    <template x-if="item.item_type === 'product'">
                                        <div class="relative w-full" x-data="{
                                            openProductSearch: false,
                                            searchQuery: item.item_code ? `${item.item_name} (${item.item_code})` : item.item_name,
                                            highlightedIndex: 0,
                                            get filteredProducts() {
                                                const q = (this.searchQuery || '').trim().toLowerCase();
                                                if (!q) return products.slice(0, 20);
                                                return products.filter(p => {
                                                    const name = (p.name || '').toLowerCase();
                                                    const sku = (p.sku || '').toLowerCase();
                                                    const part = (p.part_number || '').toLowerCase();
                                                    return name.includes(q) || sku.includes(q) || part.includes(q);
                                                }).slice(0, 20);
                                            },
                                            selectProduct(p) {
                                                onSelectItem(index, p.id, 'product');
                                                this.searchQuery = p.name + (p.sku ? ' (' + p.sku + ')' : '');
                                                this.openProductSearch = false;
                                            },
                                            clear() {
                                                item.item_id = null;
                                                item.item_code = '';
                                                item.item_name = '';
                                                item.description = '';
                                                item.unit_price = 0;
                                                this.searchQuery = '';
                                                this.openProductSearch = false;
                                                calculateLineTotal(item);
                                            },
                                            onKeydown(e) {
                                                if (!this.openProductSearch) {
                                                    if (e.key === 'ArrowDown' || e.key === 'Enter') {
                                                        this.openProductSearch = true;
                                                        e.preventDefault();
                                                    }
                                                    return;
                                                }
                                                if (e.key === 'ArrowDown') {
                                                    e.preventDefault();
                                                    const count = this.filteredProducts.length;
                                                    if (count > 0) this.highlightedIndex = (this.highlightedIndex + 1) % count;
                                                } else if (e.key === 'ArrowUp') {
                                                    e.preventDefault();
                                                    const count = this.filteredProducts.length;
                                                    if (count > 0) this.highlightedIndex = (this.highlightedIndex - 1 + count) % count;
                                                } else if (e.key === 'Enter') {
                                                    e.preventDefault();
                                                    const pList = this.filteredProducts;
                                                    if (pList.length > 0 && this.highlightedIndex >= 0 && this.highlightedIndex < pList.length) {
                                                        this.selectProduct(pList[this.highlightedIndex]);
                                                    }
                                                } else if (e.key === 'Escape') {
                                                    this.openProductSearch = false;
                                                }
                                            }
                                        }" @click.outside="openProductSearch = false">
                                            <div class="relative">
                                                <input 
                                                    type="text" 
                                                    x-model="searchQuery"
                                                    @focus="openProductSearch = true"
                                                    @input="openProductSearch = true; highlightedIndex = 0"
                                                    @keydown="onKeydown($event)"
                                                    placeholder="Search product name, SKU or part number..."
                                                    class="block w-full pr-7 pl-2.5 py-1.5 text-xs text-gray-900 border border-gray-300 rounded focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[34px]"
                                                    autocomplete="off"
                                                />
                                                <button 
                                                    type="button" 
                                                    x-show="searchQuery.length > 0" 
                                                    @click="clear()" 
                                                    class="absolute inset-y-0 right-0 pr-2 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer"
                                                    title="Clear selection"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div 
                                                x-show="openProductSearch" 
                                                x-transition
                                                class="absolute left-0 right-0 z-50 mt-1 bg-white border border-gray-200 rounded shadow-lg max-h-52 overflow-y-auto min-w-[260px]"
                                                style="display: none;"
                                            >
                                                <template x-for="(p, pIdx) in filteredProducts" :key="p.id">
                                                    <div 
                                                        @click="selectProduct(p)"
                                                        @mouseenter="highlightedIndex = pIdx"
                                                        :class="{'bg-[#0B1849]/10 text-[#0B1849]': highlightedIndex === pIdx, 'text-gray-700': highlightedIndex !== pIdx}"
                                                        class="px-3 py-2 cursor-pointer border-b border-gray-50 last:border-b-0 text-left transition"
                                                    >
                                                        <div class="font-semibold text-xs text-gray-900" x-text="p.name"></div>
                                                        <div class="text-[11px] text-gray-500 flex items-center justify-between mt-0.5">
                                                            <span class="font-mono bg-gray-100 px-1 rounded text-[10px]" x-show="p.sku" x-text="'SKU: ' + p.sku"></span>
                                                            <span class="text-[10px]" x-text="'Price: LKR ' + (parseFloat(p.selling_price) || 0).toFixed(2) + (p.available_stock !== undefined ? ' • Stock: ' + p.available_stock : '')"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <div x-show="filteredProducts.length === 0" class="px-3 py-2.5 text-xs text-gray-400 text-center">
                                                    No products found matching "<span class="font-semibold" x-text="searchQuery"></span>"
                                                </div>
                                            </div>
                                        </div>
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
                        Create Invoice
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal: New Customer & Vehicle -->
    <div 
        x-show="showCustomerModal" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        @keydown.escape.window="showCustomerModal = false"
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Backdrop -->
            <div 
                x-show="showCustomerModal" 
                x-transition:enter="ease-out duration-200" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-150" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-xs" 
                @click="showCustomerModal = false"
                aria-hidden="true"
            ></div>

            <!-- Modal Card -->
            <div 
                x-show="showCustomerModal" 
                x-transition:enter="ease-out duration-200" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-150" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                @click.stop
                class="relative z-10 w-full max-w-lg p-6 my-8 text-left bg-white rounded-2xl shadow-2xl border border-gray-200 space-y-5 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h3 class="text-base font-black text-[#0B1849] tracking-tight">New Customer &amp; Vehicle</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Enter details to automatically register upon saving.</p>
                    </div>
                    <button 
                        type="button" 
                        @click="showCustomerModal = false" 
                        class="text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100 cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Inline Error Alert inside modal -->
                <div x-show="modalError" x-cloak class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center space-x-2">
                    <svg class="w-4 h-4 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span x-text="modalError" class="font-medium"></span>
                </div>

                <!-- Form Fields -->
                <div class="space-y-4 text-xs">
                    <!-- 1. Vehicle Number (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Vehicle Number <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <input 
                            type="text" 
                            x-model="modalVehicleNumber" 
                            placeholder="e.g. WP CAB-1234" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 font-mono uppercase border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white shadow-2xs"
                        />
                    </div>

                    <!-- 2. Customer Name (Required) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Customer Name <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            x-model="modalCustomerName" 
                            placeholder="Full customer name" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white shadow-2xs"
                        />
                    </div>

                    <!-- 3. Mobile Number (Required) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Mobile Number <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            x-model="modalCustomerMobile" 
                            placeholder="07XXXXXXXX" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white shadow-2xs"
                        />
                    </div>

                    <!-- 4. Email Address (Optional) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Email Address <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <input 
                            type="email" 
                            x-model="modalCustomerEmail" 
                            placeholder="customer@example.com" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white shadow-2xs"
                        />
                    </div>

                    <!-- 5. Vehicle Type (Dropdown, Optional if vehicle empty; Required if vehicle entered) -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Vehicle Type 
                            <span x-show="modalVehicleNumber.trim().length > 0" class="text-rose-500">*</span>
                            <span x-show="modalVehicleNumber.trim().length === 0" class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <select 
                            x-model="modalVehicleTypeId" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white shadow-2xs"
                        >
                            <option value="">-- Select Vehicle Type --</option>
                            <template x-for="t in vehicleTypes" :key="t.id">
                                <option :value="t.id" x-text="t.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 pt-3 border-t border-gray-100">
                    <button 
                        type="button" 
                        @click="showCustomerModal = false" 
                        class="px-4 py-2 text-xs font-bold text-gray-700 hover:text-gray-900 hover:bg-gray-100 border border-gray-300 rounded-lg transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        @click="saveModalDetails()" 
                        class="px-5 py-2 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-2xs transition cursor-pointer"
                    >
                        Use Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function invoiceForm(config) {
    return {
        services: config.services || [],
        products: config.products || [],
        vehicles: config.vehicles || [],
        vehicleTypes: config.vehicleTypes || [],
        customers: config.customers || [],

        // Modal State
        showCustomerModal: false,
        modalCustomerName: '',
        modalCustomerMobile: '',
        modalCustomerEmail: '',
        modalVehicleNumber: '',
        modalVehicleTypeId: '',
        modalError: '',

        // Selected / Active state for form submission & summary display
        selectedCustomerId: config.selectedCustomerId || '',
        selectedVehicleId: config.selectedVehicleId || '',
        selectedCustomerName: '{{ old('new_customer_name', '') }}',
        selectedCustomerPhone: '{{ old('new_customer_mobile', '') }}',
        selectedVehicleNumber: '{{ old('new_vehicle_number', '') }}',
        selectedVehicleType: '',
        isNewCustomer: {{ old('new_customer_name') ? 'true' : 'false' }},

        // Hidden form fields for new customer/vehicle
        newCustomerName: '{{ old('new_customer_name', '') }}',
        newCustomerMobile: '{{ old('new_customer_mobile', '') }}',
        newCustomerEmail: '{{ old('new_customer_email', '') }}',
        newVehicleNumber: '{{ old('new_vehicle_number', '') }}',
        newVehicleTypeId: '{{ old('new_vehicle_type_id', '') }}',

        discountType: '{{ old('discount_type', 'none') }}',
        discountValue: '{{ old('discount_value', 0) }}',
        taxPercentage: '{{ old('tax_percentage', 0) }}',
        additionalCharges: '{{ old('additional_charges', 0) }}',

        items: [],

        init() {
            if (this.selectedCustomerId && !this.selectedCustomerName) {
                const c = this.customers.find(x => x.id == this.selectedCustomerId);
                if (c) {
                    this.selectedCustomerName = c.customer_name;
                    this.selectedCustomerPhone = c.phone || '';
                }
            }
            if (this.selectedVehicleId) {
                const v = this.vehicles.find(x => x.id == this.selectedVehicleId);
                if (v) {
                    if (!this.selectedVehicleNumber) {
                        this.selectedVehicleNumber = v.vehicle_number;
                        const vt = this.vehicleTypes.find(t => t.id == v.vehicle_type_id);
                        this.selectedVehicleType = vt ? vt.name : (v.vehicle_type ? v.vehicle_type.name : '');
                    }
                    if (!this.selectedCustomerId && v.customer_id) {
                        this.selectedCustomerId = String(v.customer_id);
                    }
                    if (!this.selectedCustomerName && (v.customer || v.customer_id)) {
                        const owner = v.customer || this.customers.find(c => c.id == v.customer_id);
                        if (owner) {
                            this.selectedCustomerName = owner.customer_name;
                            this.selectedCustomerPhone = owner.phone || '';
                        }
                    }
                }
            }
            if (this.newVehicleTypeId && !this.selectedVehicleType) {
                const vt = this.vehicleTypes.find(t => t.id == this.newVehicleTypeId);
                if (vt) {
                    this.selectedVehicleType = vt.name;
                }
            }

            const cInput = document.getElementById('hidden_customer_id');
            if (cInput && this.selectedCustomerId) cInput.value = this.selectedCustomerId;
            const vInput = document.getElementById('hidden_vehicle_id');
            if (vInput && this.selectedVehicleId) vInput.value = this.selectedVehicleId;

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

        openNewCustomerModal() {
            this.modalCustomerName = this.newCustomerName || this.selectedCustomerName || '';
            this.modalCustomerMobile = this.newCustomerMobile || this.selectedCustomerPhone || '';
            this.modalCustomerEmail = this.newCustomerEmail || '';
            this.modalVehicleNumber = this.newVehicleNumber || this.selectedVehicleNumber || '';
            this.modalVehicleTypeId = this.newVehicleTypeId || '';
            this.modalError = '';
            this.showCustomerModal = true;
        },

        saveModalDetails() {
            this.modalError = '';
            const name = (this.modalCustomerName || '').trim();
            const mobile = (this.modalCustomerMobile || '').trim();
            const vehNum = (this.modalVehicleNumber || '').trim().toUpperCase();
            const vehTypeId = this.modalVehicleTypeId;

            if (!name) {
                this.modalError = 'Customer Name is required.';
                return;
            }
            if (!mobile) {
                this.modalError = 'Mobile Number is required.';
                return;
            }
            if (vehNum && !vehTypeId) {
                this.modalError = 'Vehicle Type must be selected when a Vehicle Number is provided.';
                return;
            }

            this.isNewCustomer = true;
            this.selectedCustomerId = '';
            this.selectedVehicleId = '';
            this.newCustomerName = name;
            this.newCustomerMobile = mobile;
            this.newCustomerEmail = (this.modalCustomerEmail || '').trim();
            this.newVehicleNumber = vehNum;
            this.newVehicleTypeId = vehTypeId || '';

            this.selectedCustomerName = name;
            this.selectedCustomerPhone = mobile;
            this.selectedVehicleNumber = vehNum;
            const vt = this.vehicleTypes.find(t => t.id == vehTypeId);
            this.selectedVehicleType = vt ? vt.name : '';

            const cInput = document.getElementById('hidden_customer_id');
            if (cInput) cInput.value = '';
            const vInput = document.getElementById('hidden_vehicle_id');
            if (vInput) vInput.value = '';

            this.showCustomerModal = false;
        },

        handleEntitySelect(item) {
            if (!item) return;
            this.isNewCustomer = false;

            let custId = item.customer_id ? String(item.customer_id) : '';
            let vehId = item.vehicle_id ? String(item.vehicle_id) : '';

            if (!vehId && item.id && String(item.id).startsWith('v_')) {
                vehId = String(item.id).substring(2);
            }
            if (!custId && item.id && String(item.id).startsWith('c_')) {
                custId = String(item.id).substring(2);
            }

            // If a vehicle was selected but customer_id was not on the payload, lookup from loaded vehicles
            if (vehId && !custId && this.vehicles && this.vehicles.length) {
                const foundV = this.vehicles.find(v => v.id == vehId);
                if (foundV && foundV.customer_id) {
                    custId = String(foundV.customer_id);
                    if (!item.customer_name && foundV.customer) {
                        item.customer_name = foundV.customer.customer_name;
                        item.customer_phone = foundV.customer.phone || '';
                    }
                }
            }

            this.selectedCustomerId = custId;
            this.selectedVehicleId = vehId;
            this.selectedCustomerName = item.customer_name || '';
            this.selectedCustomerPhone = item.customer_phone || '';
            this.selectedVehicleNumber = item.vehicle_number || '';
            this.selectedVehicleType = item.vehicle_type || '';

            this.newCustomerName = '';
            this.newCustomerMobile = '';
            this.newCustomerEmail = '';
            this.newVehicleNumber = '';
            this.newVehicleTypeId = '';

            const cInput = document.getElementById('hidden_customer_id');
            if (cInput) cInput.value = this.selectedCustomerId;
            const vInput = document.getElementById('hidden_vehicle_id');
            if (vInput) vInput.value = this.selectedVehicleId;
        },

        clearCustomerAndVehicle() {
            this.isNewCustomer = false;
            this.selectedCustomerId = '';
            this.selectedVehicleId = '';
            this.selectedCustomerName = '';
            this.selectedCustomerPhone = '';
            this.selectedVehicleNumber = '';
            this.selectedVehicleType = '';
            this.newCustomerName = '';
            this.newCustomerMobile = '';
            this.newCustomerEmail = '';
            this.newVehicleNumber = '';
            this.newVehicleTypeId = '';

            const cInput = document.getElementById('hidden_customer_id');
            if (cInput) cInput.value = '';
            const vInput = document.getElementById('hidden_vehicle_id');
            if (vInput) vInput.value = '';

            this.$dispatch('searchable-select-clear-customer_search');
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
