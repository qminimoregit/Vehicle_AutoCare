@extends('layouts.app')

@section('title', 'Direct Sale (POS)')

@section('content')
<!-- External Google Font & Bootstrap Icons -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
/* ==========================================================================
   VIRAN AUTO CARE DIRECT SALE (POS) — IMAGE 1 HIGH FIDELITY DESIGN SYSTEM
   ========================================================================== */
:root {
    --lv-navy: #0D1946;
    --lv-navy-dark: #071030;
    --lv-blue: #2563EB;
    --lv-blue-hover: #1D4ED8;
    --lv-border: #E5EAF2;
    --lv-bg: #F5F7FB;
    --lv-text: #111827;
    --lv-muted: #64748B;
}

.pos-workspace-wrap {
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    color: var(--lv-text);
}

.thin-scrollbar::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}
.thin-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.thin-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.thin-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Job Card Lovable Item List Styles */
.lv-cart-group {
    margin-top: 4px;
}
.lv-cart-group h4 {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .7px;
    color: var(--lv-muted);
    font-weight: 700;
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
    padding: 2px 6px;
}

.lv-line {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 6px;
    border-bottom: 1px solid var(--lv-border);
    transition: background-color 0.15s;
}
.lv-line:hover {
    background-color: #FAFBFD;
}
.lv-line:last-child {
    border-bottom: 0;
}
.lv-line-main {
    flex: 1 1 auto;
    min-width: 0;
}
.lv-line-name {
    font-weight: 600;
    font-size: 13px;
    line-height: 1.25;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: var(--lv-text);
}
.lv-line-tech {
    font-size: 11px;
    color: var(--lv-muted);
    margin-top: 1px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.lv-line-total {
    flex: 0 0 auto;
    text-align: right;
    min-width: 80px;
    font-weight: 700;
    font-size: 13px;
    color: var(--lv-navy);
    font-variant-numeric: tabular-nums;
}
.lv-rm-btn {
    flex: 0 0 auto;
    border: 0;
    background: none;
    color: #94A3B8;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s;
}
.lv-rm-btn:hover {
    background: #FEE2E2;
    color: #DC2626;
}
.lv-edit-btn {
    flex: 0 0 auto;
    border: 0;
    background: none;
    color: #94A3B8;
    width: 22px;
    height: 22px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 11px;
    transition: all 0.15s;
}
.lv-edit-btn:hover {
    background: #EFF6FF;
    color: #2563EB;
}

/* Stepper Quantity Controls [-] [qty] [+] */
.lv-stepper {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--lv-border);
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
    flex-shrink: 0;
}
.lv-step-btn {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: var(--lv-text);
    cursor: pointer;
    font-size: 11px;
    transition: background-color 0.15s, color 0.15s;
    padding: 0;
}
.lv-step-btn:hover:not(:disabled) {
    background: #EFF6FF;
    color: var(--lv-blue);
}
.lv-step-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}
.lv-step-val {
    min-width: 22px;
    padding: 0 4px;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    color: var(--lv-text);
    font-variant-numeric: tabular-nums;
}

@media (max-width: 480px) {
    .lv-line {
        flex-wrap: wrap;
        gap: 6px 8px;
        padding: 8px 6px;
    }
    .lv-line-main {
        flex: 1 0 100%;
        width: 100%;
    }
    .lv-stepper {
        margin-right: auto;
    }
    .lv-line-total {
        min-width: auto;
        text-align: right;
    }
}
</style>

<div 
    x-data="{
        // Search & Filter State
        searchTerm: '',
        selectedCategory: 'all',
        viewMode: 'grid', // 'grid' | 'list'
        
        // Products initialized with dynamic category classification
        products: {{ Js::from($products) }}.map(p => {
            let name = (p.name || p.product_name || '').toLowerCase();
            let cat = 'Spare Parts';
            if (name.includes('filter') || name.includes('pad') || name.includes('brake') || name.includes('shoe') || name.includes('battery') || name.includes('plug') || name.includes('wiper') || name.includes('belt') || name.includes('bulb')) {
                cat = 'Spare Parts';
            } else if (name.includes('oil') || name.includes('fluid') || name.includes('coolant') || name.includes('spray') || name.includes('lubricant')) {
                cat = 'Lubricants';
            } else if (name.includes('assistant') || name.includes('accessory') || name.includes('camera') || name.includes('cover') || name.includes('perfume') || name.includes('cleaner')) {
                cat = 'Accessories';
            }
            p.category = cat;
            return p;
        }),
        
        // Cart State
        cart: [],
        customerId: '',
        customerName: '',
        customerPhone: '',
        customerEmail: '',
        discount: 0,
        tax: 0,
        paymentMethod: 'cash',
        amountPaid: '',
        notes: '',

        // UI & Modal State
        isSubmitting: false,
        mobileView: 'products', // 'products' | 'cart'
        showSuccessModal: false,
        completedSale: null,

        // Price Edit Modal State (Job Card Style)
        showPriceModal: false,
        editingItemIndex: null,
        editingPriceItem: {
            name: '',
            sku: '',
            quantity: 1,
            unit_price: 0,
            original_price: 0,
            unit_cost: null,
        },

        // Sharing State
        activeShareTab: '', // '' | 'email' | 'whatsapp'
        emailRecipient: '',
        isSendingEmail: false,
        emailSuccessMessage: '',
        emailErrorMessage: '',
        whatsappRecipientPhone: '',
        whatsappMessageText: '',
        copiedWhatsApp: false,

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
            this.customerEmail = '';
            this.notes = '';
            this.showSuccessModal = false;
            this.completedSale = null;
            this.activeShareTab = '';
            this.emailRecipient = '';
            this.emailSuccessMessage = '';
            this.emailErrorMessage = '';
            this.whatsappRecipientPhone = '';
            this.whatsappMessageText = '';
            this.copiedWhatsApp = false;
            this.showPriceModal = false;
            this.editingItemIndex = null;
        },

        formatQty(val) {
            if (val === null || val === undefined || isNaN(val)) return '0';
            let num = parseFloat(val);
            if (Number.isInteger(num)) {
                return num.toLocaleString();
            }
            return parseFloat(num.toFixed(4)).toLocaleString();
        },

        formatMoney(val) {
            return Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        // Category options list with dynamic counts
        get categoryList() {
            let counts = { 'All Items': this.products.length };
            this.products.forEach(p => {
                counts[p.category] = (counts[p.category] || 0) + 1;
            });
            return [
                { name: 'All Items', key: 'all', count: this.products.length },
                { name: 'Spare Parts', key: 'Spare Parts', count: counts['Spare Parts'] || 0 },
                { name: 'Lubricants', key: 'Lubricants', count: counts['Lubricants'] || 0 },
                { name: 'Accessories', key: 'Accessories', count: counts['Accessories'] || 0 },
            ];
        },

        // Filtered products list
        get filteredProducts() {
            let list = this.products;

            // Category filter
            if (this.selectedCategory !== 'all') {
                list = list.filter(p => p.category === this.selectedCategory);
            }

            // Search filter
            if (!this.searchTerm.trim()) {
                return list;
            }
            let term = this.searchTerm.toLowerCase();
            return list.filter(p => {
                let name = (p.name || p.product_name || '').toLowerCase();
                let sku = (p.sku || '').toLowerCase();
                let barcode = (p.barcode || '').toLowerCase();
                let cat = (p.category || '').toLowerCase();
                return name.includes(term) || sku.includes(term) || barcode.includes(term) || cat.includes(term);
            });
        },

        // Add item to cart
        addToCart(product) {
            let stock = parseFloat(product.available_stock) || 0;
            if (stock <= 0) {
                alert('This product is currently out of stock.');
                return;
            }

            let existing = this.cart.find(item => item.product_id === product.id);
            if (existing) {
                if (existing.quantity + 1 > stock) {
                    alert('Cannot add more than available stock (' + this.formatQty(stock) + ' units).');
                    return;
                }
                existing.quantity += 1;
                existing.line_total = Math.round(existing.quantity * existing.unit_price * 100) / 100;
            } else {
                let cost = null;
                if (product.unit_cost !== undefined && product.unit_cost !== null) {
                    let pCost = parseFloat(product.unit_cost);
                    if (!isNaN(pCost) && pCost > 0) {
                        cost = pCost;
                    }
                }
                this.cart.push({
                    product_id: product.id,
                    name: product.name || product.product_name || 'Product',
                    sku: product.sku,
                    image_url: product.image_url,
                    unit_price: parseFloat(product.selling_price) || 0,
                    unit_cost: cost,
                    available_stock: stock,
                    quantity: 1,
                    line_total: parseFloat(product.selling_price) || 0,
                    showPriceEdit: false,
                });
            }
            this.recalculatePayment();
        },

        // Open Centered Edit Price Modal (matching Job Card Edit Part popup)
        openPriceEdit(index) {
            let item = this.cart[index];
            if (!item) return;
            this.editingItemIndex = index;
            let currentPrice = parseFloat(item.unit_price);
            if (isNaN(currentPrice) || currentPrice < 0) {
                currentPrice = 0;
            }
            this.editingPriceItem = {
                name: item.name,
                sku: item.sku || 'N/A',
                quantity: item.quantity || 1,
                unit_price: currentPrice,
                original_price: currentPrice,
                unit_cost: (item.unit_cost !== null && item.unit_cost !== undefined) ? Number(item.unit_cost) : null,
            };
            this.showPriceModal = true;
        },

        // Save Price Changes from Modal
        savePriceEdit() {
            if (this.editingItemIndex === null || !this.cart[this.editingItemIndex]) {
                this.showPriceModal = false;
                return;
            }
            let item = this.cart[this.editingItemIndex];
            let price = parseFloat(this.editingPriceItem.unit_price);
            if (isNaN(price) || price < 0) {
                alert('Please enter a valid selling price (0 or higher).');
                return;
            }
            item.unit_price = Math.round(price * 100) / 100;
            item.line_total = Math.round(item.quantity * item.unit_price * 100) / 100;
            this.recalculatePayment();
            this.showPriceModal = false;
            this.editingItemIndex = null;
        },

        // Cancel Price Changes
        cancelPriceEdit() {
            this.showPriceModal = false;
            this.editingItemIndex = null;
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

        // Clear cart with confirmation
        clearCart() {
            if (this.cart.length > 0 && confirm('Are you sure you want to clear the entire sale cart?')) {
                this.cart = [];
                this.amountPaid = '';
                this.discount = 0;
            }
        },

        // Financial calculations
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

        // Phone normalization helper
        normalizePhone(phone) {
            if (!phone) return '';
            let clean = phone.toString().replace(/[\s\-\(\)\.]/g, '').trim();
            if (clean.startsWith('+')) clean = clean.substring(1);
            if (clean.startsWith('00')) clean = clean.substring(2);
            if (/^0([1-9][0-9]{8})$/.test(clean)) return '94' + clean.substring(1);
            if (/^([1-9][0-9]{8})$/.test(clean)) return '94' + clean;
            if (/^94[0-9]{9}$/.test(clean)) return clean;
            let digits = clean.replace(/\D/g, '');
            return digits || '';
        },

        isValidWhatsAppPhone(phone) {
            let norm = this.normalizePhone(phone);
            return /^94[0-9]{9}$/.test(norm) || (norm.length >= 9 && norm.length <= 15);
        },

        openEmailModal() {
            this.activeShareTab = this.activeShareTab === 'email' ? '' : 'email';
            if (this.activeShareTab === 'email') {
                this.emailRecipient = this.completedSale?.customer_email || this.customerEmail || '';
                this.emailSuccessMessage = '';
                this.emailErrorMessage = '';
            }
        },

        async sendBillEmail() {
            let email = (this.emailRecipient || '').trim();
            if (!email || !email.includes('@')) {
                this.emailErrorMessage = 'Please enter a valid email address.';
                return;
            }

            this.isSendingEmail = true;
            this.emailSuccessMessage = '';
            this.emailErrorMessage = '';

            try {
                let response = await fetch(this.completedSale?.email_url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: email }),
                });

                let data = await response.json();

                if (response.ok && data.success) {
                    this.emailSuccessMessage = data.message || ('Sales Bill emailed successfully to ' + email + '.');
                    if (this.completedSale) {
                        this.completedSale.customer_email = email;
                    }
                } else {
                    this.emailErrorMessage = data.message || 'Failed to send email. Please verify mail configuration.';
                }
            } catch (err) {
                console.error(err);
                this.emailErrorMessage = 'Network error while attempting to send email. Please check server connection.';
            } finally {
                this.isSendingEmail = false;
            }
        },

        openWhatsAppModal() {
            this.activeShareTab = this.activeShareTab === 'whatsapp' ? '' : 'whatsapp';
            if (this.activeShareTab === 'whatsapp') {
                this.whatsappRecipientPhone = this.completedSale?.customer_phone || this.customerPhone || '';
                this.whatsappMessageText = this.completedSale?.whatsapp_message || '';
            }
        },

        copyWhatsAppText() {
            if (navigator.clipboard && this.whatsappMessageText) {
                navigator.clipboard.writeText(this.whatsappMessageText).then(() => {
                    this.copiedWhatsApp = true;
                    setTimeout(() => { this.copiedWhatsApp = false; }, 2500);
                });
            }
        },

        openWhatsApp() {
            let norm = this.normalizePhone(this.whatsappRecipientPhone);
            if (!norm || norm.length < 9) {
                alert('Please enter a valid mobile number (e.g. 077 123 4567).');
                return;
            }
            let url = 'https://wa.me/' + encodeURIComponent(norm) + '?text=' + encodeURIComponent(this.whatsappMessageText);
            window.open(url, '_blank');
        },

        printBill() {
            let url = this.completedSale?.bill_url || this.completedSale?.thermal_url;
            if (window.AutoCarePrinter && window.AutoCarePrinter.printUrl) {
                window.AutoCarePrinter.printUrl(url, {
                    type: 'bill',
                    title: 'POS Bill #' + (this.completedSale?.bill_number || ''),
                    documentNumber: this.completedSale?.bill_number || '',
                    fallbackUrl: url
                });
            } else {
                window.open(url, '_blank');
            }
        },

        // Checkout via AJAX
        async checkout() {
            if (this.cart.length === 0) {
                alert('Cart is empty. Please add products first.');
                return;
            }

            let paid = parseFloat(this.amountPaid);
            if (isNaN(paid) || paid < 0) {
                alert('Please enter a valid payment amount.');
                return;
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
                email: this.customerEmail || null,
                discount_amount: parseFloat(this.discount) || 0,
                tax_amount: parseFloat(this.tax) || 0,
                payment_method: this.paymentMethod,
                amount_paid: paid,
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
                    this.emailRecipient = data.customer_email || this.customerEmail || '';
                    this.whatsappRecipientPhone = data.customer_phone || this.customerPhone || '';
                    this.whatsappMessageText = data.whatsapp_message || '';
                    this.activeShareTab = '';
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
    class="pos-workspace-wrap space-y-3"
>
    <!-- Compact Top Bar: Title & Status -->
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
            <h1 class="text-lg sm:text-xl font-extrabold text-[#0D1946] tracking-tight m-0">Direct Sale (POS)</h1>
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Terminal Ready
            </span>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 hidden sm:inline">FIFO Inventory & Sales Billing</span>
            <a href="{{ route('operations.job-cards.index') }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition shadow-2xs">
                <i class="bi bi-wrench-adjustable text-blue-600"></i> Job Cards
            </a>
        </div>
    </div>

    <!-- Mobile Quick Navigation Bar (< lg) -->
    <div class="lg:hidden flex items-center p-1 rounded-xl bg-gray-200/90 shadow-2xs gap-1">
        <button 
            type="button" 
            @click="document.getElementById('pos-catalog-panel')?.scrollIntoView({ behavior: 'smooth' })"
            class="flex-1 py-2 px-3 text-xs rounded-lg transition text-center cursor-pointer flex items-center justify-center space-x-1.5 min-h-[38px] bg-white text-gray-700 hover:text-gray-900 font-semibold border border-gray-300"
        >
            <i class="bi bi-box-seam"></i>
            <span>Products (<span x-text="filteredProducts.length"></span>)</span>
        </button>

        <button 
            type="button" 
            @click="document.getElementById('pos-cart-panel')?.scrollIntoView({ behavior: 'smooth' })"
            class="flex-1 py-2 px-3 text-xs rounded-lg transition text-center cursor-pointer flex items-center justify-center space-x-1.5 min-h-[38px] bg-[#0D1946] text-white shadow-xs font-bold"
        >
            <i class="bi bi-cart3"></i>
            <span>Go to Cart (<span x-text="cart.length"></span>)</span>
            <template x-if="cart.length > 0">
                <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-500 text-white font-black" x-text="'LKR ' + grandTotal.toFixed(0)"></span>
            </template>
        </button>
    </div>

    <!-- ===================================================================
         MAIN TWO-COLUMN WORKSPACE: LEFT CATALOG (~65%) / RIGHT CART (~35%)
         =================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        <!-- LEFT COLUMN: PRODUCT CATALOG (Image 1: 4 columns on large desktop) -->
        <div id="pos-catalog-panel" class="w-full lg:col-span-7 xl:col-span-8 space-y-3">
            
            <!-- Row 1: Search Bar & Grid/List View Toggle -->
            <div class="flex items-center gap-2">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="bi bi-search text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        x-model="searchTerm" 
                        placeholder="Search products by name, SKU, or barcode..." 
                        class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0D1946] focus:border-[#0D1946] shadow-2xs h-[42px]"
                    >
                </div>

                <div class="flex bg-white border border-gray-300 rounded-xl p-1 shadow-2xs shrink-0 h-[42px]">
                    <button 
                        type="button" 
                        :class="viewMode === 'grid' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-400 hover:text-gray-700'"
                        @click="viewMode = 'grid'" 
                        class="px-2.5 rounded-lg flex items-center justify-center transition cursor-pointer"
                        title="Grid view"
                    >
                        <i class="bi bi-grid-3x3-gap text-base"></i>
                    </button>
                    <button 
                        type="button" 
                        :class="viewMode === 'list' ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-400 hover:text-gray-700'"
                        @click="viewMode = 'list'" 
                        class="px-2.5 rounded-lg flex items-center justify-center transition cursor-pointer"
                        title="List view"
                    >
                        <i class="bi bi-list-ul text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Row 2: Category Filter Buttons (Dynamic with Actual Product Counts) -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-0.5">
                <template x-for="cat in categoryList" :key="cat.key">
                    <button 
                        type="button" 
                        @click="selectedCategory = cat.key"
                        class="whitespace-nowrap px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shrink-0"
                        :class="selectedCategory === cat.key ? 'bg-[#0D1946] text-white shadow-2xs border border-[#0D1946]' : 'bg-white text-gray-700 border border-gray-200 hover:border-gray-300'"
                    >
                        <span x-text="cat.name"></span>
                        <span 
                            class="text-[10px] px-1.5 py-0.2 rounded-full font-semibold"
                            :class="selectedCategory === cat.key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'"
                            x-text="cat.count"
                        ></span>
                    </button>
                </template>
            </div>

            <!-- Product Items: GRID VIEW (Image 1: 4 Columns, Compact, Upper-Left Thumbnail) -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div 
                        class="bg-white border rounded-xl p-3 flex flex-col justify-between transition hover:shadow-md hover:border-blue-400"
                        :class="(parseFloat(product.available_stock) || 0) <= 0 ? 'border-gray-200 opacity-70 bg-gray-50/50' : 'border-gray-200'"
                    >
                        <div>
                            <!-- Upper Row: Thumbnail at Left + Name and SKU beside it -->
                            <div class="flex items-start gap-2.5">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0 overflow-hidden relative">
                                    <template x-if="product.image_url">
                                        <img 
                                            :src="product.image_url" 
                                            :alt="product.name || product.product_name" 
                                            class="w-full h-full object-contain p-1" 
                                            loading="lazy" 
                                        />
                                    </template>
                                    <template x-if="!product.image_url">
                                        <div class="flex flex-col items-center justify-center text-slate-300">
                                            <i class="bi bi-box-seam text-xl"></i>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 
                                        class="text-xs sm:text-[13px] font-bold text-gray-900 leading-snug line-clamp-2 min-h-[2rem]" 
                                        x-text="product.name || product.product_name"
                                        :title="product.name || product.product_name"
                                    ></h4>
                                    <p class="text-[10.5px] font-mono text-gray-400 mt-0.5 truncate" x-text="'SKU: ' + (product.sku || 'N/A')"></p>
                                </div>
                            </div>

                            <!-- Middle Section: Category Label & Availability Badge -->
                            <div class="mt-2.5 space-y-1">
                                <div class="text-[11px] font-medium text-gray-500 truncate" x-text="product.category || 'Spare Parts'"></div>
                                <div>
                                    <template x-if="(parseFloat(product.available_stock) || 0) > 0">
                                        <span class="inline-flex items-center text-[10.5px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 rounded px-1.5 py-0.5">
                                            Available: <span class="ml-1" x-text="product.formatted_stock || formatQty(product.available_stock)"></span>
                                        </span>
                                    </template>
                                    <template x-if="(parseFloat(product.available_stock) || 0) <= 0">
                                        <span class="inline-flex items-center text-[10.5px] font-bold text-red-700 bg-red-50 border border-red-200/60 rounded px-1.5 py-0.5">
                                            Out of Stock
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section: Price on Left, [+ Add] Button on Right -->
                        <div class="mt-3 pt-2 border-t border-gray-100 flex items-center justify-between gap-1">
                            <div>
                                <span class="text-xs sm:text-sm font-black text-[#0D1946] whitespace-nowrap" x-text="'LKR ' + parseFloat(product.selling_price).toFixed(2)"></span>
                            </div>

                            <div>
                                <template x-if="(parseFloat(product.available_stock) || 0) > 0">
                                    <button 
                                        type="button" 
                                        @click="addToCart(product)"
                                        class="inline-flex items-center justify-center px-2.5 py-1.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold rounded-lg shadow-2xs transition cursor-pointer"
                                    >
                                        <i class="bi bi-plus-lg text-[11px] mr-1"></i> Add
                                    </button>
                                </template>
                                <template x-if="(parseFloat(product.available_stock) || 0) <= 0">
                                    <button 
                                        type="button" 
                                        disabled 
                                        class="inline-flex items-center justify-center px-2.5 py-1.5 bg-gray-100 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed opacity-60"
                                    >
                                        Out
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Product Items: LIST VIEW (Compact Row Format) -->
            <div x-show="viewMode === 'list'" class="space-y-2">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div 
                        class="bg-white border rounded-xl p-2.5 flex items-center justify-between gap-3 transition hover:shadow-sm hover:border-blue-400"
                        :class="(parseFloat(product.available_stock) || 0) <= 0 ? 'border-gray-200 opacity-70 bg-gray-50/50' : 'border-gray-200'"
                    >
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0 overflow-hidden">
                                <template x-if="product.image_url">
                                    <img :src="product.image_url" :alt="product.name" class="w-full h-full object-contain p-1" loading="lazy" />
                                </template>
                                <template x-if="!product.image_url">
                                    <i class="bi bi-box-seam text-slate-300 text-lg"></i>
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate" x-text="product.name || product.product_name" :title="product.name || product.product_name"></h4>
                                <p class="text-[10.5px] font-mono text-gray-400" x-text="'SKU: ' + (product.sku || 'N/A')"></p>
                            </div>
                        </div>

                        <div class="w-36 shrink-0 hidden sm:block">
                            <div class="text-[11px] font-medium text-gray-500 truncate" x-text="product.category || 'Spare Parts'"></div>
                            <div>
                                <template x-if="(parseFloat(product.available_stock) || 0) > 0">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 rounded px-1.5 py-0.5">
                                        Available: <span x-text="product.formatted_stock || formatQty(product.available_stock)"></span>
                                    </span>
                                </template>
                                <template x-if="(parseFloat(product.available_stock) || 0) <= 0">
                                    <span class="text-[10px] font-bold text-red-700 bg-red-50 border border-red-200/60 rounded px-1.5 py-0.5">
                                        Out of Stock
                                    </span>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs sm:text-sm font-black text-[#0D1946] whitespace-nowrap" x-text="'LKR ' + parseFloat(product.selling_price).toFixed(2)"></span>
                            <template x-if="(parseFloat(product.available_stock) || 0) > 0">
                                <button 
                                    type="button" 
                                    @click="addToCart(product)"
                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold rounded-lg shadow-2xs transition cursor-pointer"
                                >
                                    <i class="bi bi-plus-lg text-[11px] mr-1"></i> Add
                                </button>
                            </template>
                            <template x-if="(parseFloat(product.available_stock) || 0) <= 0">
                                <button 
                                    type="button" 
                                    disabled 
                                    class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed opacity-60"
                                >
                                    Out
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State when no products match filter -->
            <template x-if="filteredProducts.length === 0">
                <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-400">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                        <i class="bi bi-search"></i>
                    </div>
                    <h4 class="font-bold text-gray-700 text-sm m-0">No matching products found</h4>
                    <p class="text-xs text-gray-500 mt-1">Try another keyword or select All Items.</p>
                    <button 
                        type="button" 
                        @click="searchTerm = ''; selectedCategory = 'all'" 
                        class="mt-3 px-3 py-1 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100"
                    >
                        Reset Filters
                    </button>
                </div>
            </template>
        </div>

        <!-- ===================================================================
             RIGHT COLUMN: CURRENT SALE CART (Image 1 Exact Design)
             =================================================================== -->
        <div id="pos-cart-panel" class="w-full lg:col-span-5 xl:col-span-4">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                
                <!-- 1. Cart Header (Image 1: Dark Navy Background) -->
                <div class="bg-[#0D1946] text-white px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-cart3 text-base text-blue-200"></i>
                        <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-white m-0">
                            Current Sale Cart (<span x-text="cart.length"></span>)
                        </h3>
                    </div>
                    <button 
                        type="button" 
                        @click="clearCart()" 
                        x-show="cart.length > 0"
                        class="text-xs text-blue-200 hover:text-white underline cursor-pointer font-medium"
                    >
                        Clear All
                    </button>
                </div>

                <!-- 2. Customer Details (Optional Walk-In by Default) -->
                <div class="p-3 bg-slate-50/70 border-b border-gray-200 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            Customer Details (Optional)
                        </span>
                        <span class="text-[10px] text-gray-400 font-medium">Walk-in by default</span>
                    </div>
                    <div class="space-y-1.5">
                        <input 
                            type="text" 
                            x-model="customerName"
                            placeholder="Customer Name (optional)"
                            class="block w-full py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#0D1946] focus:border-[#0D1946] min-h-[34px]"
                        >
                        <div class="grid grid-cols-2 gap-1.5">
                            <input 
                                type="text" 
                                x-model="customerPhone"
                                placeholder="Mobile Number"
                                class="py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#0D1946] focus:border-[#0D1946] min-h-[34px]"
                            >
                            <input 
                                type="email" 
                                x-model="customerEmail"
                                placeholder="Email Address"
                                class="py-1.5 px-2.5 text-xs text-gray-900 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#0D1946] focus:border-[#0D1946] min-h-[34px]"
                            >
                        </div>
                    </div>
                </div>

                <!-- 3. Cart Items List (Job Card Compact Row Structure) -->
                <div class="p-2 space-y-1">
                    <!-- Empty State -->
                    <template x-if="cart.length === 0">
                        <div class="py-8 text-center text-gray-400">
                            <i class="bi bi-cart3 text-2xl text-slate-300 block mb-1"></i>
                            <p class="text-xs font-semibold text-gray-600 m-0">Your sale cart is empty</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Click <b>+ Add</b> on any product to start a sale.</p>
                        </div>
                    </template>

                    <!-- Products & Parts Section -->
                    <div class="lv-cart-group" x-show="cart.length > 0">
                        <h4>
                            <span><i class="bi bi-box-seam text-amber-600"></i> Products & Parts (<span x-text="cart.length"></span>)</span>
                            <span>LKR <span x-text="formatMoney(subtotal)"></span></span>
                        </h4>

                        <!-- Cart Item Rows (Compact Row Layout matching Job Card) -->
                        <template x-for="(item, index) in cart" :key="item.product_id">
                            <div class="lv-line flex-wrap">
                                <!-- Product Details (Name & SKU underneath) -->
                                <div class="lv-line-main">
                                    <div class="lv-line-name" :title="item.name" x-text="item.name"></div>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <div class="lv-line-tech" x-text="'SKU: ' + (item.sku || 'N/A')"></div>
                                        @if(Auth::user()->canViewCostAndProfit())
                                            <span class="text-gray-300 text-xs">—</span>
                                            <button 
                                                type="button" 
                                                @click="openPriceEdit(index)" 
                                                class="lv-edit-btn"
                                                title="Edit selling price"
                                                aria-label="Edit selling price"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Stepper Quantity Controls [-] [qty] [+] -->
                                <div class="lv-stepper">
                                    <button 
                                        type="button" 
                                        @click="decreaseQty(index)" 
                                        class="lv-step-btn" 
                                        title="Decrease quantity"
                                    >
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <span class="lv-step-val" x-text="formatQty(item.quantity)"></span>
                                    <button 
                                        type="button" 
                                        @click="increaseQty(index)" 
                                        class="lv-step-btn" 
                                        :disabled="(parseFloat(item.available_stock) || 0) <= item.quantity"
                                        title="Increase quantity"
                                    >
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>

                                <!-- Line Total -->
                                <div class="lv-line-total" x-text="formatMoney(item.line_total)"></div>

                                <!-- Remove Button -->
                                <button 
                                    type="button" 
                                    @click="removeFromCart(index)" 
                                    class="lv-rm-btn" 
                                    title="Remove item"
                                >
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 4. Cart Totals & Payment Section (Image 1: Highlighted Strip) -->
                <div class="p-3.5 bg-slate-50/90 border-t border-gray-200 space-y-2.5">
                    <!-- Subtotal & Discount -->
                    <div class="space-y-1.5 text-xs text-gray-600">
                        <div class="flex justify-between items-center">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900" x-text="'LKR ' + formatMoney(subtotal)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Discount (LKR):</span>
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                x-model="discount" 
                                @input="recalculatePayment()"
                                class="w-24 text-right py-1 px-2 text-xs border border-gray-300 rounded bg-white font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#0D1946]"
                            >
                        </div>
                    </div>

                    <!-- Prominent Highlighted TOTAL AMOUNT Strip -->
                    <div class="bg-blue-50/80 border border-blue-200/80 rounded-xl px-3 py-2 flex items-center justify-between">
                        <span class="text-xs font-black text-[#0D1946] tracking-wider uppercase">TOTAL AMOUNT</span>
                        <span class="text-base font-black text-[#0D1946]" x-text="'LKR ' + formatMoney(grandTotal)"></span>
                    </div>

                    <!-- 5. Payment Methods (Image 1: Compact Horizontal Buttons) -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            Payment Method
                        </label>
                        <div class="grid grid-cols-5 gap-1.5">
                            <button 
                                type="button" 
                                @click="paymentMethod = 'cash'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold text-xs transition cursor-pointer"
                                :class="paymentMethod === 'cash' ? 'bg-[#0D1946] text-white border-[#0D1946] shadow-2xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            >Cash</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'card'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold text-xs transition cursor-pointer"
                                :class="paymentMethod === 'card' ? 'bg-[#0D1946] text-white border-[#0D1946] shadow-2xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            >Card</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'bank_transfer'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold text-xs transition cursor-pointer"
                                :class="paymentMethod === 'bank_transfer' ? 'bg-[#0D1946] text-white border-[#0D1946] shadow-2xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            >Transfer</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'cheque'; recalculatePayment();"
                                class="py-2 px-1 text-center rounded-lg border font-bold text-xs transition cursor-pointer"
                                :class="paymentMethod === 'cheque' ? 'bg-[#0D1946] text-white border-[#0D1946] shadow-2xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            >Cheque</button>
                            <button 
                                type="button" 
                                @click="paymentMethod = 'credit'; amountPaid = 0;"
                                class="py-2 px-1 text-center rounded-lg border font-bold text-xs transition cursor-pointer"
                                :class="paymentMethod === 'credit' ? 'bg-[#0D1946] text-white border-[#0D1946] shadow-2xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                            >Credit</button>
                        </div>
                    </div>

                    <!-- Cheque Reference Input (When Cheque selected) -->
                    <div x-show="paymentMethod === 'cheque'" class="pt-1" x-transition>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1">
                            Cheque / Bank Reference
                        </label>
                        <input 
                            type="text" 
                            x-model="notes" 
                            placeholder="e.g. CHQ-928103 / Commercial Bank" 
                            class="block w-full py-1.5 px-2.5 text-xs border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-[#0D1946]"
                        >
                    </div>

                    <!-- Amount Paid & Change (When Cash / Card / Transfer / Cheque) -->
                    <div x-show="paymentMethod !== 'credit'" class="space-y-1 pt-1" x-transition>
                        <div class="flex items-center justify-between text-xs">
                            <label class="font-bold text-[10px] uppercase tracking-wider text-gray-600">Amount Paid (LKR):</label>
                            <button 
                                type="button" 
                                @click="setExactPayment()"
                                class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer"
                            >
                                Exact Amount
                            </button>
                        </div>
                        <input 
                            type="number" 
                            step="0.01" 
                            x-model="amountPaid"
                            class="block w-full py-2 px-3 text-sm font-extrabold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0D1946] bg-white min-h-[38px]"
                        >
                        <div class="flex justify-between items-center text-xs font-bold pt-0.5" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-red-600'">
                            <span>Change / Balance:</span>
                            <span class="text-xs font-black whitespace-nowrap" x-text="'LKR ' + changeAmount.toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- 6. Complete Sale Button (Image 1: Blue Full-Width) -->
                    <div class="pt-2">
                        <button 
                            type="button" 
                            @click="checkout()"
                            :disabled="cart.length === 0 || isSubmitting"
                            class="w-full inline-flex items-center justify-center py-3 px-4 bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-extrabold text-sm rounded-xl shadow-md transition disabled:opacity-50 cursor-pointer min-h-[46px]"
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
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="bi bi-check2-circle text-base"></i>
                                    Complete Sale & Generate Bill
                                </span>
                            </template>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Cart Summary Banner (When cart has items and not on cart view) -->
    <div 
        x-show="cart.length > 0 && !showSuccessModal && mobileView !== 'cart'" 
        class="lg:hidden fixed bottom-4 inset-x-4 z-30 bg-[#0D1946] text-white p-3 rounded-2xl shadow-2xl flex items-center justify-between border border-white/20"
        style="display: none;"
    >
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-200 block">Sales Cart</span>
            <span class="text-xs font-black whitespace-nowrap" x-text="cart.length + ' item(s) • LKR ' + grandTotal.toFixed(2)"></span>
        </div>
        <button 
            type="button" 
            @click="mobileView = 'cart'; window.scrollTo({ top: 0, behavior: 'smooth' })" 
            class="inline-flex items-center px-3.5 py-1.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer"
        >
            Checkout &rarr;
        </button>
    </div>

    <!-- Sale Completed Success Modal (Preserves all Bill, Thermal Print, Email, WhatsApp, PDF & Next Sale functions) -->
    <div 
        x-show="showSuccessModal" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div 
            @click.outside="resetSale()"
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-w-[calc(100vw-2rem)] p-5 sm:p-6 text-center space-y-4 border border-gray-200 max-h-[90vh] overflow-y-auto"
        >
            <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl">
                <i class="bi bi-check-lg"></i>
            </div>

            <div>
                <h3 class="text-xl font-black text-gray-900">Sale Completed!</h3>
                <p class="text-xs text-gray-500 mt-1" x-text="completedSale?.message || 'Payment recorded and Sales Bill generated successfully.'"></p>
            </div>

            <!-- Transaction Details Box -->
            <div class="bg-slate-50 rounded-xl p-3.5 border border-gray-100 text-xs space-y-2 text-gray-700 text-left">
                <div class="flex justify-between items-center font-bold">
                    <span class="text-gray-500 uppercase text-[11px]">Bill Number:</span>
                    <span class="text-[#0D1946] font-mono text-sm font-black" x-text="completedSale?.bill_number || ('#' + (completedSale?.sale_id || ''))"></span>
                </div>
                <div class="flex justify-between items-center font-semibold">
                    <span class="text-gray-500 uppercase text-[11px]">Total Paid:</span>
                    <span class="text-emerald-700 font-bold whitespace-nowrap text-sm" x-text="'LKR ' + (completedSale?.amount_paid != null ? parseFloat(completedSale.amount_paid).toFixed(2) : '0.00')"></span>
                </div>
                <template x-if="completedSale?.balance > 0">
                    <div class="flex justify-between items-center font-semibold">
                        <span class="text-gray-500 uppercase text-[11px]">Balance Due:</span>
                        <span class="text-red-600 font-bold whitespace-nowrap text-xs" x-text="'LKR ' + parseFloat(completedSale.balance).toFixed(2)"></span>
                    </div>
                </template>
                <div class="flex justify-between items-center pt-1 border-t border-gray-200">
                    <span class="text-gray-500 uppercase text-[11px] font-bold">Payment Status:</span>
                    <span 
                        class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-extrabold uppercase tracking-wide"
                        :class="completedSale?.payment_status === 'Paid in Full' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'"
                        x-text="completedSale?.payment_status || 'Paid in Full'"
                    ></span>
                </div>
                <template x-if="completedSale?.change_amount > 0">
                    <div class="flex justify-between items-center text-emerald-700 font-bold pt-1 border-t border-gray-200">
                        <span class="text-[11px]">Change Returned:</span>
                        <span class="whitespace-nowrap" x-text="'LKR ' + parseFloat(completedSale.change_amount).toFixed(2)"></span>
                    </div>
                </template>
            </div>

            <!-- Action Buttons: 2-Column Responsive Grid -->
            <div class="grid grid-cols-2 gap-2 pt-1">
                <!-- Print Bill -->
                <button 
                    type="button" 
                    @click="printBill()"
                    class="inline-flex items-center justify-center px-3 py-2.5 bg-[#0D1946] hover:bg-[#172B63] text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer min-h-[40px] gap-1.5"
                >
                    <i class="bi bi-printer"></i>
                    <span>Print Bill</span>
                </button>

                <!-- View Bill -->
                <a 
                    :href="completedSale?.bill_url || completedSale?.thermal_url" 
                    target="_blank"
                    class="inline-flex items-center justify-center px-3 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-800 text-xs font-bold rounded-xl shadow-xs transition min-h-[40px] gap-1.5"
                >
                    <i class="bi bi-eye text-gray-500"></i>
                    <span>View Bill</span>
                </a>

                <!-- Email Bill -->
                <button 
                    type="button" 
                    @click="openEmailModal()"
                    class="inline-flex items-center justify-center px-3 py-2.5 border text-xs font-bold rounded-xl shadow-xs transition cursor-pointer min-h-[40px] gap-1.5"
                    :class="activeShareTab === 'email' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 border-blue-200 hover:bg-blue-100 text-[#0D1946]'"
                >
                    <i class="bi bi-envelope"></i>
                    <span>Email Bill</span>
                </button>

                <!-- WhatsApp -->
                <button 
                    type="button" 
                    @click="openWhatsAppModal()"
                    class="inline-flex items-center justify-center px-3 py-2.5 border text-xs font-bold rounded-xl shadow-xs transition cursor-pointer min-h-[40px] gap-1.5"
                    :class="activeShareTab === 'whatsapp' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100 text-emerald-800'"
                >
                    <i class="bi bi-whatsapp"></i>
                    <span>WhatsApp</span>
                </button>
            </div>

            <!-- Inline Email Drawer -->
            <div 
                x-show="activeShareTab === 'email'" 
                x-transition 
                class="bg-blue-50/70 border border-blue-200 rounded-xl p-3 text-left space-y-2.5"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#0D1946] uppercase tracking-wide flex items-center gap-1.5">
                        <i class="bi bi-envelope-paper text-blue-600"></i>
                        Email Sales Bill (PDF Attached)
                    </span>
                    <button type="button" @click="activeShareTab = ''" class="text-gray-400 hover:text-gray-700 text-xs font-bold cursor-pointer">
                        &times; Close
                    </button>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-gray-700">Recipient Email Address:</label>
                    <input 
                        type="email" 
                        x-model="emailRecipient" 
                        placeholder="customer@example.com"
                        class="w-full py-1.5 px-3 text-xs bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-1 focus:ring-blue-500"
                    >
                </div>

                <template x-if="emailSuccessMessage">
                    <div class="p-2 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-xs font-semibold flex items-center gap-1.5">
                        <i class="bi bi-check-circle-fill text-emerald-600"></i>
                        <span x-text="emailSuccessMessage"></span>
                    </div>
                </template>

                <template x-if="emailErrorMessage">
                    <div class="p-2 bg-red-100 border border-red-300 text-red-800 rounded-lg text-xs font-semibold flex items-center gap-1.5">
                        <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                        <span x-text="emailErrorMessage"></span>
                    </div>
                </template>

                <div class="flex items-center justify-end space-x-2 pt-1">
                    <button 
                        type="button" 
                        @click="sendBillEmail()"
                        :disabled="isSendingEmail || !emailRecipient"
                        class="inline-flex items-center px-3.5 py-1.5 bg-[#0D1946] hover:bg-[#172B63] text-white text-xs font-bold rounded-lg shadow-2xs transition disabled:opacity-50 cursor-pointer gap-1"
                    >
                        <template x-if="isSendingEmail">
                            <span class="inline-flex items-center">
                                <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </template>
                        <template x-if="!isSendingEmail">
                            <span><i class="bi bi-send"></i> Send Email</span>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Inline WhatsApp Drawer -->
            <div 
                x-show="activeShareTab === 'whatsapp'" 
                x-transition 
                class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3 text-left space-y-2.5"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900 uppercase tracking-wide flex items-center gap-1.5">
                        <i class="bi bi-whatsapp text-emerald-600"></i>
                        Share via WhatsApp (Manual)
                    </span>
                    <button type="button" @click="activeShareTab = ''" class="text-gray-400 hover:text-gray-700 text-xs font-bold cursor-pointer">
                        &times; Close
                    </button>
                </div>

                <div class="space-y-1">
                    <label class="block text-[11px] font-semibold text-gray-700">Customer Mobile Number:</label>
                    <input 
                        type="text" 
                        x-model="whatsappRecipientPhone" 
                        placeholder="e.g. 077 123 4567"
                        class="w-full py-1.5 px-3 text-xs bg-white border border-gray-300 rounded-lg text-gray-900 focus:ring-1 focus:ring-emerald-500"
                    >
                    <p class="text-[10px] text-gray-500">
                        <template x-if="isValidWhatsAppPhone(whatsappRecipientPhone)">
                            <span class="text-emerald-700 font-semibold">
                                &bull; Normalized: +<span x-text="normalizePhone(whatsappRecipientPhone)"></span> (Ready to open WhatsApp)
                            </span>
                        </template>
                        <template x-if="!isValidWhatsAppPhone(whatsappRecipientPhone) && whatsappRecipientPhone">
                            <span class="text-amber-700 font-semibold">
                                &bull; Enter standard 10-digit Sri Lankan number (e.g. 077 123 4567)
                            </span>
                        </template>
                    </p>
                </div>

                <!-- Message Preview -->
                <div class="space-y-1">
                    <div class="flex justify-between items-center text-[11px] font-semibold text-gray-600">
                        <span>Message Preview:</span>
                        <button 
                            type="button" 
                            @click="copyWhatsAppText()"
                            class="text-emerald-700 hover:underline cursor-pointer font-semibold"
                        >
                            <span x-text="copiedWhatsApp ? 'Copied!' : 'Copy Text'"></span>
                        </button>
                    </div>
                    <textarea 
                        x-model="whatsappMessageText" 
                        rows="4" 
                        class="w-full p-2 text-[11px] bg-white border border-gray-200 rounded-lg font-mono text-gray-800"
                    ></textarea>
                </div>

                <p class="text-[10.5px] text-gray-500 italic">
                    Note: WhatsApp click-to-chat opens WhatsApp Web or App with prefilled text. Cashier can review and press Send.
                </p>

                <!-- Action Buttons in WhatsApp Drawer -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-emerald-200">
                    <a 
                        :href="completedSale?.pdf_url" 
                        download
                        class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-lg shadow-2xs gap-1"
                        title="Download PDF to manually attach in WhatsApp"
                    >
                        <i class="bi bi-file-earmark-pdf"></i>
                        <span>Download PDF</span>
                    </a>

                    <button 
                        type="button" 
                        @click="openWhatsApp()"
                        :disabled="!isValidWhatsAppPhone(whatsappRecipientPhone)"
                        class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-2xs transition disabled:opacity-40 cursor-pointer gap-1"
                    >
                        <i class="bi bi-whatsapp"></i>
                        <span>Open WhatsApp &rarr;</span>
                    </button>
                </div>
            </div>

            <!-- Start Next Sale Button -->
            <div class="pt-2">
                <button 
                    type="button" 
                    @click="resetSale()"
                    class="w-full py-2.5 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition cursor-pointer flex items-center justify-center gap-1.5"
                >
                    <i class="bi bi-plus-circle"></i>
                    <span>Start Next Sale</span>
                </button>
            </div>
        </div>
    </div>

    @if(Auth::user()->canViewCostAndProfit())
        <!-- ==========================================================================
             MODAL: EDIT PRICE (MATCHING JOB CARD EDIT PART POPUP)
             ========================================================================== -->
        <div 
            x-show="showPriceModal" 
            x-transition 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0D1946]/45 backdrop-blur-xs" 
            @keydown.escape.window="cancelPriceEdit()" 
            @click="cancelPriceEdit()"
            style="display: none;"
        >
            <div 
                class="bg-white rounded-2xl w-full max-w-sm sm:max-w-md shadow-2xl border border-gray-200 overflow-hidden relative z-60" 
                @click.stop
            >
                <!-- Modal Header -->
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-[#0D1946] m-0 flex items-center gap-2">
                        <i class="bi bi-pencil-square text-blue-600"></i>
                        <span>Edit Price</span>
                    </h3>
                    <button 
                        type="button" 
                        @click="cancelPriceEdit()" 
                        class="text-gray-400 hover:text-gray-700 border-0 bg-transparent cursor-pointer p-1"
                        title="Close (Esc)"
                    >
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-5 space-y-3.5">
                    <!-- Product Details Overview Panel -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl space-y-1.5 text-xs">
                        <div>
                            <span class="text-gray-500 font-bold block mb-0.5">Product:</span>
                            <strong class="text-sm text-gray-900 block truncate" x-text="editingPriceItem.name"></strong>
                        </div>
                        <div class="flex items-center justify-between pt-1 border-t border-gray-200/60">
                            <div>
                                <span class="text-gray-500 font-bold">SKU:</span>
                                <span class="font-mono text-gray-800 font-semibold ml-1" x-text="editingPriceItem.sku || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 font-bold">Quantity:</span>
                                <span class="font-bold text-gray-900 ml-1" x-text="formatQty(editingPriceItem.quantity)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Editable Field: Unit Selling Price -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">
                            Unit Selling Price (LKR) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-xs text-gray-400 font-bold">LKR</span>
                            <input 
                                type="number" 
                                x-model.number="editingPriceItem.unit_price" 
                                min="0" 
                                step="0.01" 
                                placeholder="0.00" 
                                class="w-full text-sm border-gray-300 rounded-lg py-2 pl-12 pr-3 border font-semibold focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                @keydown.enter.prevent="savePriceEdit()"
                            >
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1">
                            This updates the selling price for this cart item without changing Master Data.
                        </p>
                    </div>

                    <!-- Profit Summary Analysis Section -->
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5 text-xs">
                        <div class="flex items-center justify-between text-gray-600 font-semibold">
                            <span>Cost Price:</span>
                            <template x-if="editingPriceItem.unit_cost !== null && editingPriceItem.unit_cost !== undefined">
                                <span class="font-bold text-gray-900" x-text="'LKR ' + formatMoney(editingPriceItem.unit_cost)"></span>
                            </template>
                            <template x-if="editingPriceItem.unit_cost === null || editingPriceItem.unit_cost === undefined">
                                <span class="text-amber-600 font-medium italic">Cost unavailable</span>
                            </template>
                        </div>
                        <div class="flex items-center justify-between text-gray-600 font-semibold">
                            <span>New Selling Price:</span>
                            <span class="font-bold text-gray-900" x-text="editingPriceItem.unit_price !== '' && !isNaN(editingPriceItem.unit_price) ? 'LKR ' + formatMoney(Number(editingPriceItem.unit_price)) : '—'"></span>
                        </div>

                        <template x-if="editingPriceItem.unit_cost !== null && editingPriceItem.unit_cost !== undefined && editingPriceItem.unit_price !== '' && !isNaN(editingPriceItem.unit_price)">
                            <div class="pt-2 border-t border-slate-200 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 font-semibold">Estimated Unit Profit:</span>
                                    <span class="font-bold" :class="(Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) < 0 ? 'text-red-600' : 'text-emerald-700'" x-text="'LKR ' + formatMoney(Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost)"></span>
                                </div>
                                <template x-if="editingPriceItem.quantity > 1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600 font-semibold">Estimated Total Profit:</span>
                                        <span class="font-bold" :class="(Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) < 0 ? 'text-red-600' : 'text-emerald-700'" x-text="'LKR ' + formatMoney((Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) * editingPriceItem.quantity)"></span>
                                    </div>
                                </template>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600 font-semibold">Profit Margin (%):</span>
                                    <span class="font-bold" :class="(Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) < 0 ? 'text-red-600' : 'text-emerald-700'" x-text="Number(editingPriceItem.unit_price) > 0 ? (((Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) / Number(editingPriceItem.unit_price)) * 100).toFixed(2) + '%' : '0.00%'"></span>
                                </div>
                                <template x-if="(Number(editingPriceItem.unit_price) - editingPriceItem.unit_cost) < 0">
                                    <div class="mt-1 px-2.5 py-1.5 bg-red-100 text-red-700 font-bold rounded-lg text-xs flex items-center gap-1.5">
                                        <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                                        <span>Below Cost — Estimated Loss</span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Modal Footer / Actions -->
                <div class="px-5 py-3.5 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button 
                        type="button" 
                        @click="cancelPriceEdit()" 
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition cursor-pointer min-h-[36px]"
                    >
                        Cancel
                    </button>
                    <button 
                        type="button" 
                        @click="savePriceEdit()" 
                        class="px-5 py-2 text-xs font-bold text-white bg-[#0D1946] hover:bg-[#1A2E70] rounded-lg shadow-xs transition cursor-pointer min-h-[36px] flex items-center gap-1.5"
                    >
                        <i class="bi bi-check2"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
