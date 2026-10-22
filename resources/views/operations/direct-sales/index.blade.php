@extends('layouts.app')

@section('title', 'Direct Sale (POS)')

@section('content')
<div 
    x-data="{
        // Search & Filter
        searchTerm: '',
        products: {{ Js::from($products) }},
        
        // Cart State
        cart: [],
        customerId: '',
        customerName: '',
        customerPhone: '',
        discount: 0,
        tax: 0,
        paymentMethod: 'cash',
        amountPaid: '',
        notes: '',

        // UI State
        isSubmitting: false,
        mobileView: 'products',
        showSuccessModal: false,
        completedSale: null,

        init() {
            window.__posCartHasItems = () => this.cart && this.cart.length > 0;
        },

        resetSale() {
            this.cart = [];
            this.amountPaid = '';
            this.discount = 0;
            this.tax = 0;
            this.customerName = '';
            this.customerPhone = '';
            this.notes = '';
            this.showSuccessModal = false;
            this.completedSale = null;
        },

        formatQty(val) {
            if (val === null || val === undefined || isNaN(val)) return '0';
            let num = parseFloat(val);
            if (Number.isInteger(num)) {
                return num.toLocaleString();
            }
            return parseFloat(num.toFixed(4)).toLocaleString();
        },

        // Filtered products list
        get filteredProducts() {
            if (!this.searchTerm.trim()) {
                return this.products;
            }
            let term = this.searchTerm.toLowerCase();
            return this.products.filter(p => {
                let name = (p.name || p.product_name || '').toLowerCase();
                let sku = (p.sku || '').toLowerCase();
                let barcode = (p.barcode || '').toLowerCase();
                return name.includes(term) || sku.includes(term) || barcode.includes(term);
            });
        },

        // Add item to cart
        addToCart(product) {
            if (product.available_stock <= 0) {
                alert('This product is currently out of stock.');
                return;
            }

            let existing = this.cart.find(item => item.product_id === product.id);
            if (existing) {
                if (existing.quantity + 1 > product.available_stock) {
                    alert('Cannot add more than available stock (' + this.formatQty(product.available_stock) + ' units).');
                    return;
                }
                existing.quantity += 1;
                existing.line_total = Math.round(existing.quantity * existing.unit_price * 100) / 100;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name || product.product_name || 'Product',
                    sku: product.sku,
                    image_url: product.image_url,
                    unit_price: parseFloat(product.selling_price) || 0,
                    available_stock: parseFloat(product.available_stock) || 0,
                    quantity: 1,
                    line_total: parseFloat(product.selling_price) || 0,
                });
            }
            this.recalculatePayment();
        },

        // Increment quantity
        increaseQty(index) {
            let item = this.cart[index];
            if (item.quantity + 1 > item.available_stock) {
                alert('Cannot exceed available stock (' + this.formatQty(item.available_stock) + ' units).');
                return;
            }
            item.quantity += 1;
            item.line_total = Math.round(item.quantity * item.unit_price * 100) / 100;
            this.recalculatePayment();
        },

        // Decrement quantity
        decreaseQty(index) {
            let item = this.cart[index];
            if (item.quantity > 1) {
                item.quantity -= 1;
                item.line_total = Math.round(item.quantity * item.unit_price * 100) / 100;
            } else {
                this.cart.splice(index, 1);
            }
            this.recalculatePayment();
        },

        // Remove item
        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.recalculatePayment();
        },

        // Clear cart
        clearCart() {
            if (this.cart.length > 0 && confirm('Clear current cart?')) {
                this.cart = [];
                this.amountPaid = '';
                this.discount = 0;
            }
        },

        // Totals calculations
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (parseFloat(item.line_total) || 0), 0);
        },

        get grandTotal() {
            let disc = parseFloat(this.discount) || 0;
            let tx = parseFloat(this.tax) || 0;
            return Math.max(0, this.subtotal - disc + tx);
        },

        get changeAmount() {
            let paid = parseFloat(this.amountPaid) || 0;
            let gt = this.grandTotal;
            if (this.paymentMethod === 'credit') {
                return 0;
            }
            return paid >= gt ? Math.round((paid - gt) * 100) / 100 : 0;
        },

        recalculatePayment() {
            if (this.paymentMethod !== 'credit' && (!this.amountPaid || parseFloat(this.amountPaid) < this.grandTotal)) {
                this.amountPaid = this.grandTotal;
            }
        },

        setExactPayment() {
            this.amountPaid = this.grandTotal;
        },

        // Submit Checkout via AJAX
        async checkout() {
            if (this.cart.length === 0) {
                alert('Cart is empty. Please add products first.');
                return;
            }

            if (this.paymentMethod !== 'credit') {
                let paid = parseFloat(this.amountPaid) || 0;
                if (paid < this.grandTotal) {
                    alert('Amount paid cannot be less than Grand Total (LKR ' + this.grandTotal.toFixed(2) + ').');
                    return;
                }
            }

            this.isSubmitting = true;

            let payload = {
                items: this.cart.map(i => ({
                    product_id: i.product_id,
                    quantity: i.quantity,
                    unit_price: i.unit_price,
                })),
                customer_id: this.customerId || null,
                customer_name: this.customerName || null,
                phone: this.customerPhone || null,
                discount_amount: parseFloat(this.discount) || 0,
                tax_amount: parseFloat(this.tax) || 0,
                payment_method: this.paymentMethod,
                amount_paid: parseFloat(this.amountPaid) || this.grandTotal,
                notes: this.notes,
            };

            try {
                let response = await fetch('{{ route('operations.direct-sales.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                let data = await response.json();

                if (response.ok && data.success) {
                    this.completedSale = data;
                    this.showSuccessModal = true;
                    this.isSubmitting = false;
                } else {
                    alert(data.message || 'Checkout failed. Please check stock and try again.');
                    this.isSubmitting = false;
                }
            } catch (err) {
                console.error(err);
                alert('Network error during checkout. Please check server connection.');
                this.isSubmitting = false;
            }
        }
    }"
    class="max-w-7xl mx-auto space-y-4"
>
    <!-- Page Header -->
    <x-page-header 
        title="Direct Sale (POS)" 
        description="Over-the-counter spare parts, lubricants, and accessory sales with instant FIFO deduction."
    />

    <!-- Mobile & Tablet View Switcher Tab Bar (< lg) -->
    <div class="lg:hidden flex items-center p-1 rounded-xl bg-gray-200/90 shadow-2xs">
        <button 
            type="button" 
            @click="mobileView = 'products'"
            :class="mobileView === 'products' ? 'bg-[#0B1849] text-white shadow-xs font-bold' : 'text-gray-700 hover:text-gray-900 font-semibold'"
            class="flex-1 py-2.5 px-3 text-xs rounded-lg transition text-center cursor-pointer flex items-center justify-center space-x-1.5 min-h-[40px]"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span>Products (<span x-text="filteredProducts.length"></span>)</span>
        </button>

        <button 
            type="button" 
            @click="mobileView = 'cart'"
            :class="mobileView === 'cart' ? 'bg-[#0B1849] text-white shadow-xs font-bold' : 'text-gray-700 hover:text-gray-900 font-semibold'"
            class="flex-1 py-2.5 px-3 text-xs rounded-lg transition text-center cursor-pointer flex items-center justify-center space-x-1.5 min-h-[40px]"
        >
            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Cart (<span x-text="cart.length"></span>)</span>
            <template x-if="cart.length > 0">
                <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-500 text-white font-black" x-text="'LKR ' + grandTotal.toFixed(0)"></span>
            </template>
        </button>
    </div>

    <!-- Split Layout: Desktop side-by-side, Mobile responsive toggle -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Product Search & Product Grid (lg:col-span-7) -->
        <div :class="mobileView === 'products' ? 'block' : 'hidden lg:block'" class="lg:col-span-7 space-y-4">
            <!-- Search & Filter Banner -->
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        x-model="searchTerm"
                        placeholder="Search product by name, SKU code, or barcode..."
                        class="block w-full pl-11 pr-4 py-3 text-sm sm:text-base text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                    >
                </div>
            </div>

            <!-- Products List / Grid -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-slate-50/70 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                        Available Products (<span x-text="filteredProducts.length"></span>)
                    </span>
                    <span class="text-xs text-gray-400">Total available stock across all active batches</span>
                </div>

                <div class="p-3.5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 max-h-[640px] overflow-y-auto thin-scrollbar">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div 
                            class="border rounded-xl p-3 flex flex-col justify-between transition hover:shadow-sm" 
                            :class="product.available_stock > 0 ? 'border-gray-200 bg-white hover:border-[#0B1849]/40' : 'border-gray-100 bg-gray-50/70 opacity-60'"
                        >
                            <!-- Top: Image + Info -->
                            <div>
                                <!-- 1. Product Image (Aspect fixed, Contain, Fallback graphic) -->
                                <div class="w-full h-32 bg-slate-50 rounded-lg border border-gray-100 flex items-center justify-center p-2 overflow-hidden relative">
                                    <template x-if="product.image_url">
                                        <img 
                                            :src="product.image_url" 
                                            :alt="product.name || product.product_name" 
                                            loading="lazy" 
                                            class="w-full h-full object-contain"
                                        />
                                    </template>
                                    <template x-if="!product.image_url">
                                        <div class="flex flex-col items-center justify-center text-slate-300">
                                            <svg class="w-9 h-9 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>

                                <!-- 2. Product Name (Always clearly visible with consistent height) -->
                                <h4 
                                    class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mt-2.5 min-h-[2.5rem]" 
                                    x-text="product.name || product.product_name"
                                    :title="product.name || product.product_name"
                                ></h4>

                                <!-- 3 & 4. Product SKU & Available Stock -->
                                <div class="mt-1 flex items-center justify-between gap-1 text-[11px]">
                                    <span class="text-gray-500 font-mono truncate" x-text="'SKU: ' + (product.sku || 'N/A')"></span>
                                    <span 
                                        class="px-2 py-0.5 rounded-full font-bold whitespace-nowrap text-[10px]"
                                        :class="product.available_stock > 0 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-red-50 text-red-800 border border-red-200'"
                                        x-text="product.available_stock > 0 ? 'Available: ' + (product.formatted_stock || formatQty(product.available_stock)) : 'Out of stock'"
                                    ></span>
                                </div>
                            </div>

                            <!-- Bottom: 5. Selling Price & 6. Add Button -->
                            <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="text-[10px] text-gray-400 block font-medium uppercase">Price</span>
                                    <span class="text-sm font-black text-[#0B1849] whitespace-nowrap" x-text="'LKR ' + parseFloat(product.selling_price).toFixed(2)"></span>
                                </div>

                                <button 
                                    type="button" 
                                    @click="addToCart(product)"
                                    :disabled="product.available_stock <= 0"
                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition disabled:opacity-40 min-h-[36px] cursor-pointer shrink-0"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="filteredProducts.length === 0">
                        <div class="col-span-full py-12 text-center text-gray-400">
                            <p class="text-sm font-semibold">No matching products found.</p>
                            <p class="text-xs mt-1">Try another keyword or add products under Suppliers & Inventory.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN (DESKTOP) / CART & PAYMENT PANEL (lg:col-span-5) -->
        <div id="pos-cart-panel" :class="mobileView === 'cart' ? 'block' : 'hidden lg:block'" class="lg:col-span-5 space-y-4">
            <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
                <!-- Cart Header -->
                <div class="px-5 py-3.5 border-b border-gray-100 bg-[#0B1849] text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button 
                            type="button" 
                            @click="mobileView = 'products'" 
                            class="lg:hidden text-xs text-blue-200 hover:text-white flex items-center cursor-pointer font-semibold mr-1"
                            title="Back to products list"
                        >
                            <svg class="w-4 h-4 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </button>
                        <svg class="w-5 h-5 text-gray-200 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-sm font-bold uppercase tracking-wider">Sale Cart (<span x-text="cart.length"></span>)</h3>
                    </div>
                    <button 
                        type="button" 
                        @click="clearCart()" 
                        x-show="cart.length > 0"
                        class="text-xs text-gray-300 hover:text-white underline cursor-pointer"
                    >
                        Clear Cart
                    </button>
                </div>

                <!-- Customer Selection (Optional for Walk-in) -->
                <div class="p-4 border-b border-gray-100 bg-slate-50/50 space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600">
                        Customer (Optional Walk-In)
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input 
                            type="text" 
                            x-model="customerName"
                            placeholder="Customer Name"
                            class="py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-md bg-white min-h-[38px]"
                        >
                        <input 
                            type="text" 
                            x-model="customerPhone"
                            placeholder="Mobile Number"
                            class="py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-md bg-white min-h-[38px]"
                        >
                    </div>
                </div>

                <!-- Cart Items List -->
                <div class="p-4 max-h-[280px] overflow-y-auto divide-y divide-gray-100 space-y-2 thin-scrollbar">
                    <template x-for="(item, index) in cart" :key="item.product_id">
                        <div class="pt-2 first:pt-0 flex items-center justify-between text-xs">
                            <div class="flex-1 pr-2">
                                <span class="font-bold text-gray-900 block truncate" x-text="item.name"></span>
                                <span class="text-[11px] text-gray-500 whitespace-nowrap" x-text="'LKR ' + item.unit_price.toFixed(2) + ' each'"></span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <!-- Quantity modifier buttons -->
                                <div class="flex items-center border border-gray-200 rounded-md overflow-hidden bg-white">
                                    <button 
                                        type="button" 
                                        @click="decreaseQty(index)" 
                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 font-bold min-h-[30px] min-w-[26px] cursor-pointer"
                                    >-</button>
                                    <span class="px-2 text-xs font-bold text-gray-900" x-text="formatQty(item.quantity)"></span>
                                    <button 
                                        type="button" 
                                        @click="increaseQty(index)" 
                                        class="px-2 py-1 text-gray-600 hover:bg-gray-100 font-bold min-h-[30px] min-w-[26px] cursor-pointer"
                                    >+</button>
                                </div>

                                <span class="w-24 text-right font-black text-gray-900 whitespace-nowrap" x-text="'LKR ' + item.line_total.toFixed(2)"></span>

                                <button 
                                    type="button" 
                                    @click="removeFromCart(index)" 
                                    class="text-gray-400 hover:text-red-600 p-1 cursor-pointer"
                                    title="Remove"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="cart.length === 0">
                        <div class="py-10 text-center text-gray-400">
                            <svg class="mx-auto h-8 w-8 text-gray-300 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <p class="text-xs font-semibold">Cart is empty</p>
                            <p class="text-[11px]">Click "Add" on any product to start a sale.</p>
                        </div>
                    </template>
                </div>

                <!-- Totals & Payment Section -->
                <div class="p-4 bg-slate-50 border-t border-gray-200 space-y-3">
                    <div class="space-y-1.5 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900 whitespace-nowrap" x-text="'LKR ' + subtotal.toFixed(2)"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Discount (LKR):</span>
                            <input 
                                type="number" 
                                step="0.01" 
                                x-model="discount" 
                                @input="recalculatePayment()"
                                class="w-24 text-right py-1 px-2 text-xs border border-gray-300 rounded bg-white font-semibold"
                            >
                        </div>
                        <div class="pt-2 border-t border-gray-200 flex justify-between text-base font-black text-[#0B1849]">
                            <span>Grand Total:</span>
                            <span class="whitespace-nowrap" x-text="'LKR ' + grandTotal.toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Payment Method Picker: Cash, Card, Transfer, Cheque, Credit -->
                    <div class="pt-2 border-t border-gray-200 space-y-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-700">
                            Payment Method
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-1.5 text-xs">
                            <button 
                                type="button" 
                                @click="paymentMethod = 'cash'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold transition cursor-pointer"
                                :class="paymentMethod === 'cash' ? 'bg-[#0B1849] text-white border-[#0B1849]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100'"
                            >Cash</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'card'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold transition cursor-pointer"
                                :class="paymentMethod === 'card' ? 'bg-[#0B1849] text-white border-[#0B1849]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100'"
                            >Card</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'bank_transfer'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold transition cursor-pointer"
                                :class="paymentMethod === 'bank_transfer' ? 'bg-[#0B1849] text-white border-[#0B1849]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100'"
                            >Transfer</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'cheque'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold transition cursor-pointer"
                                :class="paymentMethod === 'cheque' ? 'bg-[#0B1849] text-white border-[#0B1849]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100'"
                            >Cheque</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'credit'; amountPaid = 0;"
                                class="py-2 px-1 text-center rounded-lg border font-bold transition cursor-pointer"
                                :class="paymentMethod === 'credit' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100'"
                            >Credit</button>
                        </div>
                    </div>

                    <!-- Cheque Reference Input (When Cheque) -->
                    <div x-show="paymentMethod === 'cheque'" class="pt-1">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-1">
                            Cheque / Bank Reference
                        </label>
                        <input 
                            type="text" 
                            x-model="notes" 
                            placeholder="e.g. CHQ-928103 / Commercial Bank" 
                            class="block w-full py-2 px-3 text-xs border border-gray-300 rounded-lg bg-white"
                        >
                    </div>

                    <!-- Amount Paid & Change (When Cash / Card / Transfer / Cheque) -->
                    <div x-show="paymentMethod !== 'credit'" class="space-y-2 pt-1">
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold uppercase tracking-wider text-gray-700">Amount Paid (LKR):</label>
                            <button 
                                type="button" 
                                @click="setExactPayment()"
                                class="text-[11px] font-semibold text-[#0B1849] hover:underline cursor-pointer"
                            >
                                Exact Amount
                            </button>
                        </div>
                        <input 
                            type="number" 
                            step="0.01" 
                            x-model="amountPaid"
                            class="block w-full py-2.5 px-3 text-base font-extrabold text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] bg-white min-h-[44px]"
                        >
                        <div class="flex justify-between items-center text-xs font-bold pt-1" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-600'">
                            <span>Change / Balance:</span>
                            <span class="text-sm font-black whitespace-nowrap" x-text="'LKR ' + changeAmount.toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Complete Sale Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="button" 
                            @click="checkout()"
                            :disabled="cart.length === 0 || isSubmitting"
                            class="w-full inline-flex items-center justify-center py-3.5 px-4 bg-[#0B1849] hover:bg-[#15286e] text-white font-extrabold text-sm sm:text-base rounded-xl shadow-md transition disabled:opacity-50 cursor-pointer min-h-[48px]"
                        >
                            <template x-if="isSubmitting">
                                <span class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing FIFO Sale...
                                </span>
                            </template>
                            <template x-if="!isSubmitting">
                                <span class="inline-flex items-center">
                                    <svg class="w-5 h-5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Complete Sale & Invoice
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Cart Summary Banner (lg:hidden) -->
    <div 
        x-show="cart.length > 0 && !showSuccessModal && mobileView !== 'cart'" 
        class="lg:hidden fixed bottom-4 inset-x-4 z-30 bg-[#0B1849] text-white p-3.5 rounded-2xl shadow-2xl flex items-center justify-between border border-white/20 animate-fade-in"
        style="display: none;"
    >
        <div>
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-200 block">Sales Cart</span>
            <span class="text-sm font-black whitespace-nowrap" x-text="cart.length + ' item(s) • LKR ' + grandTotal.toFixed(2)"></span>
        </div>
        <button 
            type="button"
            @click="mobileView = 'cart'; window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-black rounded-xl shadow-xs transition cursor-pointer"
        >
            Checkout &rarr;
        </button>
    </div>

    <!-- Sale Completed Success Modal -->
    <div 
        x-show="showSuccessModal" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div 
            @click.outside="resetSale()"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-w-[calc(100vw-2rem)] p-5 sm:p-6 text-center space-y-4 sm:space-y-5 border border-gray-200 max-h-[90vh] overflow-y-auto"
        >
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <div>
                <h3 class="text-xl font-black text-gray-900">Sale Completed!</h3>
                <p class="text-xs text-gray-500 mt-1" x-text="completedSale?.message || 'Transaction successfully processed and inventory deducted.'"></p>
            </div>

            <div class="bg-slate-50 rounded-xl p-3.5 border border-gray-100 text-xs space-y-1.5 text-gray-700">
                <div class="flex justify-between font-bold">
                    <span>Invoice / Sale:</span>
                    <span class="text-[#0B1849]" x-text="'#' + (completedSale?.sale_id || '')"></span>
                </div>
                <div class="flex justify-between">
                    <span>Payment Method:</span>
                    <span class="uppercase font-semibold" x-text="paymentMethod"></span>
                </div>
                <div class="flex justify-between font-bold text-sm text-gray-900 pt-1 border-t border-gray-200">
                    <span>Total Paid:</span>
                    <span class="whitespace-nowrap" x-text="'LKR ' + (amountPaid ? parseFloat(amountPaid).toFixed(2) : grandTotal.toFixed(2))"></span>
                </div>
                <template x-if="changeAmount > 0">
                    <div class="flex justify-between text-emerald-700 font-bold">
                        <span>Change Returned:</span>
                        <span class="whitespace-nowrap" x-text="'LKR ' + changeAmount.toFixed(2)"></span>
                    </div>
                </template>
            </div>

            <div class="space-y-2 pt-1">
                <button 
                    type="button" 
                    @click="window.AutoCarePrinter ? window.AutoCarePrinter.printUrl(completedSale?.thermal_url || completedSale?.receipt_url, { type: 'bill', title: 'POS Bill #' + (completedSale?.invoice_number || ''), documentNumber: completedSale?.invoice_number || '', fallbackUrl: completedSale?.thermal_url || completedSale?.receipt_url }) : window.open(completedSale?.thermal_url || completedSale?.receipt_url, '_blank')"
                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-xl shadow-xs transition cursor-pointer"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print Bill
                </button>

                <a 
                    :href="completedSale?.redirect_url" 
                    class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-bold text-xs rounded-xl shadow-xs transition"
                >
                    View Full Invoice
                </a>

                <button 
                    type="button" 
                    @click="resetSale()"
                    class="w-full inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-gray-500 hover:text-gray-800 cursor-pointer"
                >
                    + Start Next Sale
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
