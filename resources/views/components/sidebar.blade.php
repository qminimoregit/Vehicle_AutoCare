@php
    $isAppointments = request()->routeIs('appointments.*');
    $isOperations = request()->routeIs('operations.*');
    $isInventory = request()->routeIs('inventory.*');
    $isBilling = request()->routeIs('billing.quotations.*', 'billing.invoices.*', 'operations.invoices.*', 'billing.payments.*');
    $isAdjustments = request()->routeIs(
        'billing.avoid-direct-sale.*',
        'billing.avoid-service-sale.*',
        'billing.invoice-date-change.*',
        'billing.multiple-invoice-payment.*'
    );
    $isPayroll = request()->routeIs('payroll.*');
    $isExpenses = request()->routeIs('expenses.*');
    $isReports = request()->routeIs('reports.*');
    $isMasterData = request()->routeIs('master-data.*');

    $defaultSection = $isOperations ? 'operations' : ($isInventory ? 'inventory' : ($isBilling ? 'billing' : ($isAdjustments ? 'adjustments' : ($isPayroll ? 'payroll' : ($isExpenses ? 'expenses' : ($isReports ? 'reports' : ($isMasterData ? 'master-data' : '')))))));
@endphp

<!-- Mobile Backdrop -->
<div 
    x-show="sidebarOpen" 
    @click="sidebarOpen = false" 
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-20 bg-gray-900/50 backdrop-blur-xs md:hidden"
    style="display: none;"
></div>

<!-- Sidebar Container -->
<aside 
    id="app-sidebar"
    data-active-section="{{ $defaultSection }}"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    class="fixed top-16 bottom-0 left-0 z-20 w-[270px] bg-white border-r border-gray-200 transition-transform duration-200 ease-in-out flex flex-col justify-between"
    x-data="{
        activeSection: '{{ $defaultSection }}',
        toggleSection(section) {
            this.activeSection = (this.activeSection === section) ? '' : section;
        }
    }"
    @navigated.window="if ($event.detail.activeSection) { activeSection = $event.detail.activeSection; }"
>
    <!-- Mobile Header with Close Button -->
    <div class="md:hidden flex items-center justify-between px-4 py-3 border-b border-gray-100 shrink-0">
        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Navigation</span>
        <button 
            type="button" 
            @click="sidebarOpen = false" 
            class="p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
            aria-label="Close sidebar"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Scrollable Navigation Menu -->
    <div id="sidebar-nav-container" class="flex-1 overflow-y-auto overflow-x-hidden py-4 px-3 space-y-5 thin-scrollbar">
        <!-- 1. OVERVIEW & APPOINTMENTS -->
        <div>
            <div class="px-3 mb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                Navigation
            </div>
            <nav class="space-y-0.5">
                <!-- 1. Dashboard -->
                @php $isDashboard = request()->routeIs('dashboard', 'home'); @endphp
                <a 
                    href="{{ route('dashboard') }}" 
                    @click="if (window.innerWidth < 768) sidebarOpen = false"
                    class="group flex items-center px-3 py-2 text-[13px] rounded-lg transition {{ $isDashboard ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                >
                    <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isDashboard ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Appointments -->
                <a 
                    href="{{ route('appointments.index') }}" 
                    @click="if (window.innerWidth < 768) sidebarOpen = false"
                    class="group flex items-center px-3 py-2 text-[13px] rounded-lg transition {{ $isAppointments ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                >
                    <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isAppointments ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="flex-1">Appointments</span>
                </a>
            </nav>
        </div>

        <!-- MANAGEMENT SECTIONS (EXACT ORDER: Operations, Suppliers & Inventory, Sales & Billing, Invoice Adjustments, Employees & Payroll, Expenses, Reports & Credit, Master Data) -->
        <div>
            <div class="px-3 mb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                Management
            </div>
            <nav class="space-y-1">
                <!-- 3. Operations Expandable Section -->
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('operations')"
                        :aria-expanded="activeSection === 'operations' ? 'true' : 'false'"
                        aria-controls="section-operations"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isOperations ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'operations' && !{{ $isOperations ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isOperations ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="truncate">Operations</span>
                        </span>
                        <svg 
                            :class="activeSection === 'operations' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Operations Sub-menu -->
                    <div 
                        id="section-operations"
                        x-show="activeSection === 'operations'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- Vehicle Registration -->
                        @php $isVehicles = request()->routeIs('operations.vehicles.*'); @endphp
                        <a 
                            href="{{ route('operations.vehicles.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isVehicles ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isVehicles ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Vehicle Registration</span>
                        </a>

                        <!-- Direct Sale -->
                        @php $isDirectSale = request()->routeIs('operations.direct-sales.*'); @endphp
                        <a 
                            href="{{ route('operations.direct-sales.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isDirectSale ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isDirectSale ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Direct Sale</span>
                        </a>

                        <!-- Job Cards -->
                        @php $isJobCards = request()->routeIs('operations.job-cards.*'); @endphp
                        <a 
                            href="{{ route('operations.job-cards.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isJobCards ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isJobCards ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Job Cards</span>
                        </a>
                    </div>
                </div>

                <!-- 4. Suppliers & Inventory Expandable Section -->
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('inventory')"
                        :aria-expanded="activeSection === 'inventory' ? 'true' : 'false'"
                        aria-controls="section-inventory"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isInventory ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'inventory' && !{{ $isInventory ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isInventory ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span class="truncate">Suppliers & Inventory</span>
                        </span>
                        <svg 
                            :class="activeSection === 'inventory' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Suppliers & Inventory Sub-menu -->
                    <div 
                        id="section-inventory"
                        x-show="activeSection === 'inventory'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- Suppliers -->
                        @php $isSuppliers = request()->routeIs('inventory.suppliers.*'); @endphp
                        <a 
                            href="{{ route('inventory.suppliers.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isSuppliers ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isSuppliers ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Suppliers</span>
                        </a>

                        <!-- Products -->
                        @php $isProducts = request()->routeIs('inventory.products.*'); @endphp
                        <a 
                            href="{{ route('inventory.products.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isProducts ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isProducts ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Products</span>
                        </a>

                        <!-- Purchasing (GRN) -->
                        @php $isGrn = request()->routeIs('inventory.grn.*'); @endphp
                        <a 
                            href="{{ route('inventory.grn.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isGrn ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isGrn ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Purchasing (GRN)</span>
                        </a>

                        <!-- Stock Report -->
                        @php $isStockReport = request()->routeIs('inventory.stock-report.*'); @endphp
                        <a 
                            href="{{ route('inventory.stock-report.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isStockReport ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isStockReport ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Stock Report</span>
                        </a>
                    </div>
                </div>

                <!-- 5. Sales & Billing Expandable Section -->
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('billing')"
                        :aria-expanded="activeSection === 'billing' ? 'true' : 'false'"
                        aria-controls="section-billing"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isBilling ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'billing' && !{{ $isBilling ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isBilling ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                            </svg>
                            <span class="truncate">Sales & Billing</span>
                        </span>
                        <svg 
                            :class="activeSection === 'billing' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Sales & Billing Sub-menu -->
                    <div 
                        id="section-billing"
                        x-show="activeSection === 'billing'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- Quotations -->
                        @php $isQuotations = request()->routeIs('billing.quotations.*'); @endphp
                        <a 
                            href="{{ route('billing.quotations.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isQuotations ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isQuotations ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Quotations</span>
                        </a>

                        <!-- Invoices -->
                        @php $isInvoices = request()->routeIs('billing.invoices.*', 'operations.invoices.*'); @endphp
                        <a 
                            href="{{ route('billing.invoices.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isInvoices ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isInvoices ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Invoices</span>
                        </a>

                        <!-- Payments -->
                        @php $isPayments = request()->routeIs('billing.payments.*'); @endphp
                        <a 
                            href="{{ route('billing.payments.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isPayments ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isPayments ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Payments</span>
                        </a>
                    </div>
                </div>

                <!-- 6. Invoice Adjustments Expandable Section -->
                @if(Auth::user() && (Auth::user()->isAdmin() || Auth::user()->hasPermission('record_payments')))
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('adjustments')"
                        :aria-expanded="activeSection === 'adjustments' ? 'true' : 'false'"
                        aria-controls="section-adjustments"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isAdjustments ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'adjustments' && !{{ $isAdjustments ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isAdjustments ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="truncate">Invoice Adjustments</span>
                        </span>
                        <svg 
                            :class="activeSection === 'adjustments' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Invoice Adjustments Sub-menu -->
                    <div 
                        id="section-adjustments"
                        x-show="activeSection === 'adjustments'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- Avoid Direct Sale (Admin Only) -->
                        @if(Auth::user()->isAdmin())
                        @php $isAvoidDirect = request()->routeIs('billing.avoid-direct-sale.*'); @endphp
                        <a 
                            href="{{ route('billing.avoid-direct-sale.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isAvoidDirect ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isAvoidDirect ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Avoid Direct Sale</span>
                        </a>
                        @endif

                        <!-- Avoid Service Sale (Admin Only) -->
                        @if(Auth::user()->isAdmin())
                        @php $isAvoidService = request()->routeIs('billing.avoid-service-sale.*'); @endphp
                        <a 
                            href="{{ route('billing.avoid-service-sale.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isAvoidService ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isAvoidService ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Avoid Service Sale</span>
                        </a>
                        @endif

                        <!-- Invoice Date Change (Admin Only) -->
                        @if(Auth::user()->isAdmin())
                        @php $isDateChange = request()->routeIs('billing.invoice-date-change.*'); @endphp
                        <a 
                            href="{{ route('billing.invoice-date-change.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isDateChange ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isDateChange ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Invoice Date Change</span>
                        </a>
                        @endif

                        <!-- Multiple Invoice Payment -->
                        @if(Auth::user()->isAdmin() || Auth::user()->hasPermission('record_payments'))
                        @php $isMultiPayment = request()->routeIs('billing.multiple-invoice-payment.*'); @endphp
                        <a 
                            href="{{ route('billing.multiple-invoice-payment.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isMultiPayment ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isMultiPayment ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Multiple Invoice Payment</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- 7. Employees & Payroll Expandable Section -->
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('payroll')"
                        :aria-expanded="activeSection === 'payroll' ? 'true' : 'false'"
                        aria-controls="section-payroll"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isPayroll ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'payroll' && !{{ $isPayroll ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isPayroll ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="truncate">Employees & Payroll</span>
                        </span>
                        <svg 
                            :class="activeSection === 'payroll' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Employees & Payroll Sub-menu -->
                    <div 
                        id="section-payroll"
                        x-show="activeSection === 'payroll'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- Employee Registration -->
                        @php $isEmployees = request()->routeIs('payroll.employees.*'); @endphp
                        <a 
                            href="{{ route('payroll.employees.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isEmployees ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isEmployees ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Employee Registration</span>
                        </a>

                        <!-- Employee Attendance -->
                        @php $isAttendance = request()->routeIs('payroll.attendance.*'); @endphp
                        <a 
                            href="{{ route('payroll.attendance.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isAttendance ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isAttendance ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Employee Attendance</span>
                        </a>

                        <!-- Employee Salary -->
                        @if(Auth::user() && (Auth::user()->isAdmin() || Auth::user()->hasPermission('view_salary')))
                            @php $isSalary = request()->routeIs('payroll.salary.*'); @endphp
                            <a 
                                href="{{ route('payroll.salary.index') }}" 
                                @click="if (window.innerWidth < 768) sidebarOpen = false"
                                class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isSalary ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                            >
                                <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isSalary ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                                <span class="truncate">Employee Salary</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- 8. Expenses Expandable Section -->
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('expenses')"
                        :aria-expanded="activeSection === 'expenses' ? 'true' : 'false'"
                        aria-controls="section-expenses"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isExpenses ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'expenses' && !{{ $isExpenses ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isExpenses ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="truncate">Expenses</span>
                        </span>
                        <svg 
                            :class="activeSection === 'expenses' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Expenses Sub-menu -->
                    <div 
                        id="section-expenses"
                        x-show="activeSection === 'expenses'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- Expense Categories -->
                        @php $isCategories = request()->routeIs('expenses.categories.*'); @endphp
                        <a 
                            href="{{ route('expenses.categories.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isCategories ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isCategories ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Expense Categories</span>
                        </a>

                        <!-- Expense Records -->
                        @php $isRecords = request()->routeIs('expenses.records.*') || request()->routeIs('expenses.staff.*') || request()->routeIs('expenses.shop.*') || request()->routeIs('expenses.show') || request()->routeIs('expenses.edit'); @endphp
                        <a 
                            href="{{ route('expenses.records.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isRecords ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isRecords ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Expense Records</span>
                        </a>
                    </div>
                </div>

                <!-- 9. Reports & Credit Expandable Section -->
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('reports')"
                        :aria-expanded="activeSection === 'reports' ? 'true' : 'false'"
                        aria-controls="section-reports"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isReports ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'reports' && !{{ $isReports ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isReports ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="truncate">Reports & Credit</span>
                        </span>
                        <svg 
                            :class="activeSection === 'reports' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Reports & Credit Sub-menu -->
                    <div 
                        id="section-reports"
                        x-show="activeSection === 'reports'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- 1. Stock Report -->
                        @php $isStockReport = request()->routeIs('reports.stock.*'); @endphp
                        <a 
                            href="{{ route('reports.stock.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isStockReport ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isStockReport ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Stock Report</span>
                        </a>

                        <!-- 2. Stock Value Report (Admin Only) -->
                        @if(Auth::user()->isAdmin())
                        @php $isStockValue = request()->routeIs('reports.stock-value.*'); @endphp
                        <a 
                            href="{{ route('reports.stock-value.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isStockValue ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isStockValue ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Stock Value Report</span>
                        </a>
                        @endif

                        <!-- 3. Sales Report -->
                        @php $isSalesReport = request()->routeIs('reports.sales.*'); @endphp
                        <a 
                            href="{{ route('reports.sales.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isSalesReport ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isSalesReport ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Sales Report</span>
                        </a>

                        <!-- 4. Profit & Loss (Admin Only) -->
                        @if(Auth::user()->isAdmin())
                        @php $isProfitLoss = request()->routeIs('reports.profit-loss.*'); @endphp
                        <a 
                            href="{{ route('reports.profit-loss.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isProfitLoss ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isProfitLoss ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Profit & Loss</span>
                        </a>
                        @endif

                        <!-- 5. Vehicle Service Report -->
                        @php $isVehicleService = request()->routeIs('reports.vehicle-service.*'); @endphp
                        <a 
                            href="{{ route('reports.vehicle-service.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isVehicleService ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isVehicleService ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Vehicle Service Report</span>
                        </a>

                        <!-- 6. Money Book -->
                        @php $isMoneyBook = request()->routeIs('reports.money-book.*'); @endphp
                        <a 
                            href="{{ route('reports.money-book.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isMoneyBook ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isMoneyBook ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Money Book</span>
                        </a>

                        <!-- 7. Credit Management -->
                        @php $isCreditManagement = request()->routeIs('reports.credit-management.*'); @endphp
                        <a 
                            href="{{ route('reports.credit-management.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isCreditManagement ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isCreditManagement ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Credit Management</span>
                        </a>
                    </div>
                </div>

                <!-- 10. Master Data Expandable Section -->
                <div>
                    <button 
                        type="button" 
                        @click="toggleSection('master-data')"
                        :aria-expanded="activeSection === 'master-data' ? 'true' : 'false'"
                        aria-controls="section-master-data"
                        class="w-full flex items-center justify-between px-3 py-2 text-[13px] rounded-lg transition group cursor-pointer focus:outline-none {{ $isMasterData ? 'text-[#0B1849] font-semibold bg-[#EEF1FA]/40' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        :class="activeSection === 'master-data' && !{{ $isMasterData ? 'true' : 'false' }} ? 'bg-slate-50 text-gray-900 font-semibold' : ''"
                    >
                        <span class="flex items-center min-w-0">
                            <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isMasterData ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h6" />
                            </svg>
                            <span class="truncate">Master Data</span>
                        </span>
                        <svg 
                            :class="activeSection === 'master-data' ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                            class="w-3.5 h-3.5 ml-2 shrink-0 transform transition-transform duration-200" 
                            fill="none" 
                            viewBox="0 0 24 24" 
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Master Data Sub-menu -->
                    <div 
                        id="section-master-data"
                        x-show="activeSection === 'master-data'" 
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-1 space-y-0.5 pl-3 border-l-2 border-gray-100 ml-4"
                        style="display: none;"
                    >
                        <!-- Service Lists -->
                        @php $isServices = request()->routeIs('master-data.services.*'); @endphp
                        <a 
                            href="{{ route('master-data.services.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isServices ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isServices ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Service Lists</span>
                        </a>

                        <!-- Vehicle Types -->
                        @php $isVehicleTypes = request()->routeIs('master-data.vehicle-types.*'); @endphp
                        <a 
                            href="{{ route('master-data.vehicle-types.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isVehicleTypes ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isVehicleTypes ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Vehicle Types</span>
                        </a>

                        <!-- Manage Service Payment -->
                        @php $isServicePayments = request()->routeIs('master-data.service-payments.*'); @endphp
                        <a 
                            href="{{ route('master-data.service-payments.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isServicePayments ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isServicePayments ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Manage Service Payment</span>
                        </a>

                        <!-- Company Details -->
                        @php $isCompany = request()->routeIs('master-data.company-details.*'); @endphp
                        <a 
                            href="{{ route('master-data.company-details.edit') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isCompany ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isCompany ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Company Details</span>
                        </a>

                        <!-- Search Client SMS -->
                        @php $isSms = request()->routeIs('master-data.client-sms.*'); @endphp
                        <a 
                            href="{{ route('master-data.client-sms.index') }}" 
                            @click="if (window.innerWidth < 768) sidebarOpen = false"
                            class="group flex items-center px-2.5 py-1.5 text-[13px] rounded-lg transition {{ $isSms ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                        >
                            <span class="w-1.5 h-1.5 rounded-full mr-2 shrink-0 {{ $isSms ? 'bg-[#0B1849]' : 'bg-gray-300 group-hover:bg-gray-400' }}"></span>
                            <span class="truncate">Search Client SMS</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        @if(Auth::user() && Auth::user()->isAdmin())
            <!-- ADMINISTRATION (Admin Only) -->
            <div>
                <div class="px-3 mb-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                    Administration
                </div>
                <nav class="space-y-0.5">
                    <!-- Staff Accounts -->
                    @php $isStaff = request()->routeIs('admin.staff.*'); @endphp
                    <a 
                        href="{{ route('admin.staff.index') }}" 
                        @click="if (window.innerWidth < 768) sidebarOpen = false"
                        class="group flex items-center px-3 py-2 text-[13px] rounded-lg transition {{ $isStaff ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                    >
                        <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isStaff ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="truncate">Staff Accounts</span>
                    </a>

                    <!-- Activity Logs -->
                    @php $isLogs = request()->routeIs('admin.activity-logs.*'); @endphp
                    <a 
                        href="{{ route('admin.activity-logs.index') }}" 
                        @click="if (window.innerWidth < 768) sidebarOpen = false"
                        class="group flex items-center px-3 py-2 text-[13px] rounded-lg transition {{ $isLogs ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                    >
                        <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isLogs ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="truncate">Activity Logs</span>
                    </a>

                    <!-- Printer Settings -->
                    @php $isPrinters = request()->routeIs('admin.printer-settings.*'); @endphp
                    <a 
                        href="{{ route('admin.printer-settings.index') }}" 
                        @click="if (window.innerWidth < 768) sidebarOpen = false"
                        class="group flex items-center px-3 py-2 text-[13px] rounded-lg transition {{ $isPrinters ? 'bg-[#EEF1FA] text-[#0B1849] font-semibold' : 'text-[#4A4A4A] font-medium hover:bg-slate-50 hover:text-gray-900' }}"
                    >
                        <svg class="mr-2.5 h-4 w-4 shrink-0 {{ $isPrinters ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span class="truncate">Printer Settings</span>
                    </a>
                </nav>
            </div>
        @endif
    </div>

    <!-- Compact Clean Footer -->
    <div class="p-3 border-t border-gray-100 bg-white shrink-0">
        <div class="flex items-center justify-between text-[11px] text-gray-500 font-medium px-1">
            <span class="inline-flex items-center text-emerald-600 font-medium">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                System Online
            </span>
            <span class="text-[10px] text-gray-400 font-mono">
                AutoCare v1.0
            </span>
        </div>
    </div>
</aside>
