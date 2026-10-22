@extends('layouts.app')

@section('title', 'Company & Vehicles')

@section('content')
<script>
function companyVehicles() {
    return {
        // Selected Company State
        selectedCompanyId: {{ $selectedCompany ? $selectedCompany->id : 'null' }},

        // Modals
        showAddCompanyModal: false,
        showEditCompanyModal: false,
        showAddVehicleModal: false,
        showEditVehicleModal: false,
        showHistoryModal: false,
        showInvoicesModal: false,

        // Add Company State
        companyAddForm: {
            company_name: '',
            registration_number: '',
            customer_name: '',
            phone: '',
            email: '',
            address: '',
            credit_limit: 0,
            payment_terms: '30_days',
            custom_days: 30,
            status: 'active',
            notes: ''
        },
        companyAddErrors: {},
        isSubmittingAddCompany: false,

        // Errors & Submitting states
        companyErrors: {},
        isSubmittingCompany: false,
        vehicleErrors: {},
        isSubmittingVehicle: false,

        init() {
            this.$watch('showAddCompanyModal || showEditCompanyModal || showAddVehicleModal || showEditVehicleModal || showHistoryModal || showInvoicesModal', (isOpen) => {
                document.body.classList.toggle('overflow-hidden', Boolean(isOpen));
            });
        },

        // Add/Edit Company Form State
        companyForm: {
            id: null,
            company_name: '',
            registration_number: '',
            customer_name: '',
            phone: '',
            email: '',
            address: '',
            credit_limit: 0,
            payment_terms: '30_days',
            custom_days: 30,
            status: 'active',
            notes: ''
        },

        // Add/Edit Vehicle Form State
        vehicleForm: {
            id: null,
            company_id: {{ $selectedCompany ? $selectedCompany->id : 'null' }},
            company_name: {{ Js::from($selectedCompany ? $selectedCompany->company_name : '') }},
            vehicle_number: '',
            vehicle_type_id: {{ Js::from($vehicleTypes->first()?->id ? (string) $vehicleTypes->first()->id : '') }},
            brand: '',
            model: '',
            year_of_manufacture: '',
            driver_name: '',
            driver_mobile: '',
            current_mileage: '',
            next_service_mileage: '',
            notes: '',
            transfer_confirmed: 0
        },

        // Vehicle Live Duplicate / Transfer Check
        checkingVehicle: false,
        vehicleCheckResult: null,

        async checkVehiclePlate() {
            let num = (this.vehicleForm.vehicle_number || '').trim();
            if (num.length < 3) {
                this.vehicleCheckResult = null;
                return;
            }
            this.checkingVehicle = true;
            try {
                let res = await fetch(`{{ route('operations.company-vehicles.check-vehicle') }}?number=${encodeURIComponent(num)}&company_id=${this.vehicleForm.company_id || ''}`);
                let data = await res.json();
                this.vehicleCheckResult = data;
                if (data.exists) {
                    if (data.is_same_company) {
                        // Already belongs to this company
                    } else {
                        // Belongs to another customer/company - offer transfer
                        if (!this.vehicleForm.brand) this.vehicleForm.brand = data.brand || '';
                        if (!this.vehicleForm.model) this.vehicleForm.model = data.model || '';
                        if (!this.vehicleForm.vehicle_type_id) this.vehicleForm.vehicle_type_id = data.vehicle_type_id || '';
                        if (!this.vehicleForm.driver_name) this.vehicleForm.driver_name = data.driver_name || '';
                        if (!this.vehicleForm.driver_mobile) this.vehicleForm.driver_mobile = data.driver_mobile || '';
                        if (!this.vehicleForm.current_mileage) this.vehicleForm.current_mileage = data.current_mileage || '';
                    }
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.checkingVehicle = false;
            }
        },

        // Vehicle Service History Modal Data
        historyVehicle: null,
        historyRecords: [],
        loadingHistory: false,

        async openVehicleHistory(vehicleId) {
            this.loadingHistory = true;
            this.historyVehicle = null;
            this.historyRecords = [];
            this.showHistoryModal = true;

            try {
                let res = await fetch(`{{ url('operations/company-vehicles/vehicles') }}/${vehicleId}/history`);
                let data = await res.json();
                this.historyVehicle = data.vehicle;
                this.historyRecords = data.history || [];
            } catch (e) {
                console.error(e);
                alert('Could not load vehicle service history.');
            } finally {
                this.loadingHistory = false;
            }
        },

        // Company Invoices Modal Data
        invoicesList: [],
        invoicesSummary: { total_amount: 0, total_paid: 0, total_balance: 0 },
        loadingInvoices: false,
        invoiceFilters: {
            date_from: '',
            date_to: '',
            vehicle_id: '',
            status: 'all'
        },

        async openInvoicesModal() {
            if (!this.selectedCompanyId) return;
            this.showInvoicesModal = true;
            await this.loadCompanyInvoices();
        },

        async loadCompanyInvoices() {
            if (!this.selectedCompanyId) return;
            this.loadingInvoices = true;
            let params = new URLSearchParams({
                date_from: this.invoiceFilters.date_from || '',
                date_to: this.invoiceFilters.date_to || '',
                vehicle_id: this.invoiceFilters.vehicle_id || '',
                status: this.invoiceFilters.status || 'all'
            });

            try {
                let res = await fetch(`{{ url('operations/company-vehicles/companies') }}/${this.selectedCompanyId}/invoices?${params.toString()}`);
                let data = await res.json();
                this.invoicesList = data.invoices || [];
                this.invoicesSummary = data.summary || { total_amount: 0, total_paid: 0, total_balance: 0 };
            } catch (e) {
                console.error(e);
            } finally {
                this.loadingInvoices = false;
            }
        },

        // Quick Mileage Add in Vehicle modal
        addMileage(km) {
            let current = parseInt(this.vehicleForm.current_mileage) || 0;
            if (current > 0) {
                this.vehicleForm.next_service_mileage = current + km;
            } else {
                alert('Please enter Current Mileage first.');
            }
        },

        // Open Add Company Modal
        openAddCompany() {
            this.companyAddErrors = {};
            this.isSubmittingAddCompany = false;
            this.companyAddForm = {
                company_name: '',
                registration_number: '',
                customer_name: '',
                phone: '',
                email: '',
                address: '',
                credit_limit: 0,
                payment_terms: '30_days',
                custom_days: 30,
                status: 'active',
                notes: ''
            };
            this.showAddCompanyModal = true;
        },

        async submitAddCompany(event) {
            this.companyAddErrors = {};
            this.isSubmittingAddCompany = true;

            let form = event.target;
            let url = form.action || '{{ route('operations.company-vehicles.companies.store') }}';
            let formData = new FormData(form);
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            try {
                let res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                let data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        this.companyAddErrors = data.errors;
                    } else if (data.message) {
                        this.companyAddErrors = { general: [data.message] };
                    } else {
                        this.companyAddErrors = { general: ['An unexpected error occurred. Please try again.'] };
                    }
                    return;
                }

                // Success! Close modal and refresh company list with the newly created company selected
                this.showAddCompanyModal = false;
                let newCompanyId = data.company_id || (data.company ? data.company.id : '');
                let successMessage = data.message || 'Company registered successfully.';
                let targetUrl = new URL('{{ route('operations.company-vehicles.index') }}', window.location.origin);
                if (newCompanyId) {
                    targetUrl.searchParams.set('selected_id', newCompanyId);
                }
                targetUrl.searchParams.set('success_msg', successMessage);
                window.location.href = targetUrl.toString();
            } catch (err) {
                console.error(err);
                this.companyAddErrors = { general: ['Network or server communication error. Please try again.'] };
            } finally {
                this.isSubmittingAddCompany = false;
            }
        },

        // Open Edit Company Modal
        openEditCompany(data) {
            this.companyErrors = {};
            this.isSubmittingCompany = false;
            this.companyForm = {
                id: data.id,
                company_name: data.company_name || '',
                registration_number: data.registration_number || '',
                customer_name: data.customer_name || '',
                phone: data.phone || '',
                email: data.email || '',
                address: data.address || '',
                credit_limit: data.credit_limit || 0,
                payment_terms: data.payment_terms || '30_days',
                custom_days: data.custom_days || 30,
                status: data.status || 'active',
                notes: data.notes || ''
            };
            this.showEditCompanyModal = true;
        },

        async submitEditCompany(event) {
            this.companyErrors = {};
            if (!this.companyForm.id) {
                this.companyErrors = { general: ['Invalid company selected for editing.'] };
                return;
            }
            this.isSubmittingCompany = true;

            let form = event.target;
            let url = '{{ url('operations/company-vehicles/companies') }}/' + this.companyForm.id;
            let formData = new FormData(form);
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            try {
                let res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                let data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        this.companyErrors = data.errors;
                    } else if (data.message) {
                        this.companyErrors = { general: [data.message] };
                    } else {
                        this.companyErrors = { general: ['An unexpected error occurred. Please try again.'] };
                    }
                    return;
                }

                // Success! Close modal and refresh page to show updated company details
                this.showEditCompanyModal = false;
                let targetUrl = new URL('{{ route('operations.company-vehicles.index') }}', window.location.origin);
                targetUrl.searchParams.set('selected_id', this.companyForm.id);
                targetUrl.searchParams.set('success_msg', data.message || 'Company profile updated successfully.');
                window.location.href = targetUrl.toString();
            } catch (err) {
                console.error(err);
                this.companyErrors = { general: ['Network or server communication error. Please try again.'] };
            } finally {
                this.isSubmittingCompany = false;
            }
        },

        // Open Add Vehicle Modal for selected company
        openAddVehicle(companyId, companyName) {
            this.vehicleErrors = {};
            this.isSubmittingVehicle = false;
            this.vehicleForm = {
                id: null,
                company_id: companyId,
                company_name: companyName,
                vehicle_number: '',
                vehicle_type_id: {{ Js::from($vehicleTypes->first()?->id ? (string) $vehicleTypes->first()->id : '') }},
                brand: '',
                model: '',
                year_of_manufacture: '',
                driver_name: '',
                driver_mobile: '',
                current_mileage: '',
                next_service_mileage: '',
                notes: '',
                transfer_confirmed: 0
            };
            this.vehicleCheckResult = null;
            this.showAddVehicleModal = true;
        },

        // Open Edit Vehicle Modal
        openEditVehicle(v) {
            this.vehicleErrors = {};
            this.isSubmittingVehicle = false;
            this.vehicleForm = {
                id: v.id,
                company_id: v.customer_id,
                company_name: {{ Js::from($selectedCompany ? $selectedCompany->company_name : '') }},
                vehicle_number: v.vehicle_number || '',
                vehicle_type_id: String(v.vehicle_type_id || ''),
                brand: v.brand || '',
                model: v.model || '',
                year_of_manufacture: v.year_of_manufacture || '',
                driver_name: v.driver_name || '',
                driver_mobile: v.driver_mobile || '',
                current_mileage: v.current_mileage || '',
                next_service_mileage: v.next_service_mileage || '',
                notes: v.notes || '',
                transfer_confirmed: 0
            };
            this.showEditVehicleModal = true;
        },

        async submitEditVehicle(event) {
            this.vehicleErrors = {};
            this.isSubmittingVehicle = true;

            let form = event.target;
            let url = form.action;
            let formData = new FormData(form);
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            try {
                let res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                });

                let data = await res.json().catch(() => ({}));

                if (!res.ok) {
                    if (res.status === 422 && data.errors) {
                        this.vehicleErrors = data.errors;
                    } else if (data.message) {
                        this.vehicleErrors = { general: [data.message] };
                    } else {
                        this.vehicleErrors = { general: ['An unexpected error occurred. Please try again.'] };
                    }
                    return;
                }

                // Success! Close modal and refresh to display updated vehicle details
                this.showEditVehicleModal = false;
                let targetUrl = new URL('{{ route('operations.company-vehicles.index') }}', window.location.origin);
                targetUrl.searchParams.set('selected_id', this.vehicleForm.company_id);
                targetUrl.searchParams.set('success_msg', data.message || 'Vehicle details updated successfully.');
                window.location.href = targetUrl.toString();
            } catch (err) {
                console.error(err);
                this.vehicleErrors = { general: ['Network or server communication error. Please try again.'] };
            } finally {
                this.isSubmittingVehicle = false;
            }
        }
    };
}

window.companyVehicles = companyVehicles;
if (window.Alpine) {
    window.Alpine.data('companyVehicles', companyVehicles);
} else {
    document.addEventListener('alpine:init', () => {
        window.Alpine.data('companyVehicles', companyVehicles);
    });
}
</script>

<div 
    x-data="companyVehicles()"
    class="space-y-6 max-w-7xl mx-auto"
>
    <!-- FLASH SUCCESS & ERROR ALERTS -->
    @if(request('success_msg') || session('success'))
        <div x-data="{ show: true }" x-show="show" class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-semibold">{{ request('success_msg') ?? session('success') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-xs">
            <div class="flex items-center space-x-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
            <button type="button" @click="show = false" class="text-rose-600 hover:text-rose-800 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-[#0B1849]"></span>
                <h1 class="text-2xl font-black text-[#0B1849] tracking-tight">Company & Vehicles</h1>
            </div>
            <p class="text-sm text-gray-500 mt-0.5">
                Manage company customers, registered company vehicles, and company billing accounts.
            </p>
        </div>

        <div class="flex items-center space-x-2.5">
            <button 
                type="button" 
                @click="openAddCompany()"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm rounded-lg shadow-sm transition cursor-pointer"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Add Company
            </button>
        </div>
    </div>

    <!-- MAIN SEARCH & FILTER BAR -->
    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-xs">
        <form action="{{ route('operations.company-vehicles.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Search Company / Vehicle Number / Mobile..." 
                    class="block w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white font-medium"
                >
            </div>

            <div class="flex items-center gap-2">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg transition cursor-pointer shadow-xs"
                >
                    Search
                </button>
                @if($search)
                    <a 
                        href="{{ route('operations.company-vehicles.index') }}" 
                        class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition"
                    >
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 2-COLUMN LAYOUT: COMPANIES TABLE (LEFT) & SELECTED COMPANY DETAIL VIEW (RIGHT) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT: COMPANY LIST TABLE (lg:col-span-5) -->
        <div class="lg:col-span-5 bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="px-4 py-3.5 border-b border-gray-100 bg-slate-50/70 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Company Customers ({{ $companies->count() }})</h2>
                </div>
            </div>

            <div class="divide-y divide-gray-100 max-h-[700px] overflow-y-auto">
                @forelse($companies as $company)
                    @php 
                        $isSelected = $selectedCompany && $selectedCompany->id === $company->id;
                        $creditAcc = $company->creditAccount;
                        $vehiclesCount = $company->vehicles_count;
                        $outstanding = $creditAcc ? (float) $creditAcc->outstanding_balance : 0.00;
                        $creditStatus = $company->status === 'active' ? ($creditAcc?->status ?? 'active') : 'inactive';
                    @endphp
                    <div 
                        class="p-4 transition cursor-pointer {{ $isSelected ? 'bg-[#EEF1FA]/80 border-l-4 border-l-[#0B1849]' : 'hover:bg-slate-50' }}"
                        @click="window.location.href = '{{ route('operations.company-vehicles.index', ['selected_id' => $company->id, 'search' => $search]) }}'"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-bold text-gray-900 truncate flex items-center">
                                    {{ $company->company_name }}
                                    @if($company->registration_number)
                                        <span class="ml-1.5 text-[11px] font-normal text-gray-400">({{ $company->registration_number }})</span>
                                    @endif
                                </h3>
                                <p class="text-xs text-gray-600 mt-0.5 truncate">
                                    <span class="font-medium text-gray-700">Contact:</span> {{ $company->customer_name }}
                                </p>
                                <p class="text-xs text-gray-600 truncate">
                                    <span class="font-medium text-gray-700">Mobile:</span> {{ $company->phone }}
                                </p>
                            </div>

                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $creditStatus === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($creditStatus) }}
                            </span>
                        </div>

                        <!-- Stats Row -->
                        <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between text-xs text-gray-600">
                            <div>
                                <span class="font-semibold text-gray-800">{{ $vehiclesCount }}</span> {{ Str::plural('Vehicle', $vehiclesCount) }}
                            </div>
                            <div>
                                Outstanding: <strong class="{{ $outstanding > 0 ? 'text-rose-600 font-bold' : 'text-gray-800' }}">LKR {{ number_format($outstanding, 2) }}</strong>
                            </div>
                            <div class="flex items-center space-x-1.5" @click.stop>
                                <a 
                                    href="{{ route('operations.company-vehicles.index', ['selected_id' => $company->id, 'search' => $search]) }}"
                                    class="px-2 py-1 text-[11px] font-semibold text-[#0B1849] bg-white border border-[#0B1849]/30 rounded hover:bg-[#EEF1FA] transition"
                                >
                                    View / Manage
                                </a>
                                <button 
                                    type="button" 
                                    @click="openEditCompany({{ Js::from([
                                        'id' => $company->id,
                                        'company_name' => $company->company_name,
                                        'registration_number' => $company->registration_number,
                                        'customer_name' => $company->customer_name,
                                        'phone' => $company->phone,
                                        'email' => $company->email,
                                        'address' => $company->address,
                                        'credit_limit' => $creditAcc?->credit_limit ?? 0,
                                        'payment_terms' => $company->payment_terms_code,
                                        'custom_days' => $creditAcc?->payment_terms_days ?? 30,
                                        'status' => $company->status,
                                        'notes' => $company->notes
                                    ]) }})"
                                    class="px-2 py-1 text-[11px] font-semibold text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition cursor-pointer"
                                >
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="mx-auto h-9 w-9 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="mt-2 text-sm font-semibold text-gray-700">No company customers found.</p>
                        <p class="text-xs text-gray-400 mt-0.5">Click [+ Add Company] to register the first customer company.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT: SELECTED COMPANY DETAILS & VEHICLES (lg:col-span-7) -->
        <div class="lg:col-span-7 space-y-6">
            @if($selectedCompany)
                @php
                    $creditAcc = $selectedCompany->creditAccount;
                    $creditLimit = $creditAcc ? (float) $creditAcc->credit_limit : 0.00;
                    $outstanding = $creditAcc ? (float) $creditAcc->outstanding_balance : 0.00;
                    $availableCredit = $creditAcc ? (float) $creditAcc->available_credit : 0.00;
                    $vehicles = $selectedCompany->vehicles;
                @endphp

                <!-- 1. COMPANY INFORMATION CARD -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-start justify-between pb-3 border-b border-gray-100">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400 block">Customer Company</span>
                            <h2 class="text-lg font-black text-gray-900 flex items-center gap-2">
                                {{ $selectedCompany->company_name }}
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $selectedCompany->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ ucfirst($selectedCompany->status) }}
                                </span>
                            </h2>
                            @if($selectedCompany->registration_number)
                                <p class="text-xs text-gray-500 font-mono mt-0.5">BRN / Reg: {{ $selectedCompany->registration_number }}</p>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2">
                            <button 
                                type="button" 
                                @click="openEditCompany({{ Js::from([
                                    'id' => $selectedCompany->id,
                                    'company_name' => $selectedCompany->company_name,
                                    'registration_number' => $selectedCompany->registration_number,
                                    'customer_name' => $selectedCompany->customer_name,
                                    'phone' => $selectedCompany->phone,
                                    'email' => $selectedCompany->email,
                                    'address' => $selectedCompany->address,
                                    'credit_limit' => $creditLimit,
                                    'payment_terms' => $selectedCompany->payment_terms_code,
                                    'custom_days' => $creditAcc?->payment_terms_days ?? 30,
                                    'status' => $selectedCompany->status,
                                    'notes' => $selectedCompany->notes
                                ]) }})"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer shadow-2xs"
                            >
                                <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Edit Profile
                            </button>
                        </div>
                    </div>

                    <!-- Information Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5 pt-4 text-xs">
                        <div>
                            <span class="text-gray-400 uppercase font-semibold text-[10px] block">Contact Person</span>
                            <span class="font-bold text-gray-900 block mt-0.5">{{ $selectedCompany->customer_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold text-[10px] block">Mobile Number</span>
                            <span class="font-bold text-gray-900 block mt-0.5">{{ $selectedCompany->phone }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold text-[10px] block">Email</span>
                            <span class="font-medium text-gray-800 block mt-0.5 truncate">{{ $selectedCompany->email ?: 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold text-[10px] block">Address</span>
                            <span class="font-medium text-gray-800 block mt-0.5">{{ $selectedCompany->address ?: 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold text-[10px] block">Payment Terms</span>
                            <span class="font-bold text-[#0B1849] block mt-0.5">{{ ucfirst(str_replace('_', ' ', $selectedCompany->payment_terms_text)) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 uppercase font-semibold text-[10px] block">Total Registered Vehicles</span>
                            <span class="font-bold text-gray-900 block mt-0.5">{{ $vehicles->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. COMPANY VEHICLES SECTION -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 bg-slate-50/70 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#0B1849]"></span>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">
                                Registered Company Vehicles ({{ $vehicles->count() }})
                            </h3>
                        </div>

                        <button 
                            type="button" 
                            @click="openAddVehicle({{ $selectedCompany->id }}, {{ Js::from($selectedCompany->company_name) }})"
                            class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-lg shadow-xs transition cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Vehicle
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                            <thead class="bg-gray-50/80 text-gray-600 font-bold uppercase text-[10px] tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Vehicle Number</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Driver</th>
                                    <th class="px-4 py-3">Last Service</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-800">
                                @forelse($vehicles as $v)
                                    @php
                                        $lastJob = $v->lastCompletedJob;
                                    @endphp
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-4 py-3 font-mono font-bold text-gray-900">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="text-sm text-[#0B1849]">{{ $v->vehicle_number }}</span>
                                            </div>
                                            @if($v->make_model)
                                                <div class="text-[11px] font-sans font-normal text-gray-500">{{ $v->make_model }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-gray-700">
                                            {{ $v->vehicleType?->name ?? 'Vehicle' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($v->driver_name)
                                                <div class="font-semibold text-gray-900">{{ $v->driver_name }}</div>
                                                @if($v->driver_mobile)
                                                    <div class="text-[11px] text-gray-500 font-mono">{{ $v->driver_mobile }}</div>
                                                @endif
                                            @else
                                                <span class="text-gray-400 italic">Not Assigned</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($lastJob && $lastJob->date)
                                                <div class="font-semibold text-emerald-800">{{ $lastJob->date->format('d M Y') }}</div>
                                                <div class="text-[10px] text-gray-500 font-mono">#{{ $lastJob->job_number }}</div>
                                            @else
                                                <span class="text-gray-400 italic">Not Serviced</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center space-x-1.5">
                                                <a 
                                                    href="{{ route('operations.vehicles.index', ['search' => $v->vehicle_number]) }}"
                                                    title="Start Job Card for this vehicle"
                                                    class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-bold text-[11px] transition shadow-2xs"
                                                >
                                                    Start Job
                                                </a>
                                                <button 
                                                    type="button" 
                                                    @click="openVehicleHistory({{ $v->id }})"
                                                    class="px-2 py-1 bg-[#EEF1FA] hover:bg-[#dbe2f6] text-[#0B1849] rounded font-semibold text-[11px] transition cursor-pointer"
                                                >
                                                    View Job History
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="openEditVehicle({{ Js::from([
                                                        'id' => $v->id,
                                                        'customer_id' => $v->customer_id,
                                                        'vehicle_number' => $v->vehicle_number,
                                                        'vehicle_type_id' => $v->vehicle_type_id,
                                                        'brand' => $v->brand,
                                                        'model' => $v->model,
                                                        'year_of_manufacture' => $v->year_of_manufacture,
                                                        'driver_name' => $v->driver_name,
                                                        'driver_mobile' => $v->driver_mobile,
                                                        'current_mileage' => $v->current_mileage,
                                                        'next_service_mileage' => $v->next_service_mileage,
                                                        'notes' => $v->notes
                                                    ]) }})"
                                                    class="px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-semibold text-[11px] transition cursor-pointer"
                                                >
                                                    Edit
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <p class="font-semibold text-gray-700">No vehicles registered under this company yet.</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">Click [+ Add Vehicle] above to register or link company vehicles.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. SIMPLE COMPANY BILLING SUMMARY CARD -->
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">
                                Company Billing & Credit Summary
                            </h3>
                        </div>

                        <span class="text-xs font-semibold text-gray-500">
                            Terms: {{ ucfirst(str_replace('_', ' ', $selectedCompany->payment_terms_text)) }}
                        </span>
                    </div>

                    <!-- 3 Balance Columns -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-4">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-3.5 text-center">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 block">Credit Limit</span>
                            <span class="text-base font-black text-gray-900 mt-1 block">LKR {{ number_format($creditLimit, 2) }}</span>
                        </div>

                        <div class="bg-rose-50/70 border border-rose-200 rounded-xl p-3.5 text-center">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 block">Outstanding Balance</span>
                            <span class="text-base font-black text-rose-800 mt-1 block">LKR {{ number_format($outstanding, 2) }}</span>
                        </div>

                        <div class="bg-emerald-50/70 border border-emerald-200 rounded-xl p-3.5 text-center">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 block">Available Credit</span>
                            <span class="text-base font-black text-emerald-800 mt-1 block">LKR {{ number_format($availableCredit, 2) }}</span>
                        </div>
                    </div>

                    <!-- Billing Actions -->
                    <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2.5">
                        <div class="flex items-center space-x-2">
                            <button 
                                type="button" 
                                @click="openInvoicesModal()"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold text-xs rounded-lg transition shadow-2xs cursor-pointer"
                            >
                                <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                View Invoices
                            </button>

                            <a 
                                href="{{ route('operations.company-vehicles.companies.statement', $selectedCompany) }}"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-xs rounded-lg transition shadow-2xs cursor-pointer"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                View Statement
                            </a>
                        </div>

                        <div>
                            <a 
                                href="{{ route('billing.multiple-invoice-payment.index', ['customer_id' => $selectedCompany->id, 'tab' => 'bulk']) }}"
                                class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition shadow-2xs cursor-pointer"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Pay Multiple Invoices
                            </a>
                        </div>
                    </div>
                </div>

            @else
                <div class="bg-white border border-gray-200 rounded-xl p-12 text-center text-gray-500 shadow-xs">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-3 text-base font-bold text-gray-800">No Company Selected</h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
                        Please select a company from the list on the left to inspect its registered vehicles and credit billing account, or register a new company.
                    </p>
                    <button 
                        type="button" 
                        @click="openAddCompany()"
                        class="mt-4 px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-xs rounded-lg transition"
                    >
                        + Add New Company
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 1: ADD COMPANY                                     -->
    <!-- ======================================================== -->
    <div 
        x-show="showAddCompanyModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak
        @keydown.escape.window="showAddCompanyModal = false"
    >
        <!-- Pure Dim Backdrop (NO backdrop-blur, NO overlapping dialog) -->
        <div 
            x-show="showAddCompanyModal" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showAddCompanyModal = false"
            class="fixed inset-0 bg-gray-900/60 transition-opacity"
            aria-hidden="true"
        ></div>

        <!-- Center Container -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <!-- Modal Dialog Panel (Sharp, elevated with relative z-10 and pointer-events-auto) -->
            <div 
                x-show="showAddCompanyModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative z-10 pointer-events-auto transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-200 flex flex-col max-h-[90vh]"
            >
                <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        <h3 class="text-base font-bold tracking-tight">Register New Company</h3>
                    </div>
                    <button type="button" @click="showAddCompanyModal = false" class="text-gray-300 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form 
                    action="{{ route('operations.company-vehicles.companies.store') }}" 
                    method="POST" 
                    @submit.prevent="submitAddCompany($event)"
                    class="overflow-y-auto flex-1 p-6 space-y-4"
                >
                    @csrf

                    <!-- Inline Validation Errors Container -->
                    <template x-if="Object.keys(companyAddErrors).length > 0">
                        <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-red-800">
                                <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Please fix the following errors:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 ml-1">
                                <template x-for="(errList, field) in companyAddErrors" :key="field">
                                    <template x-for="err in errList" :key="err">
                                        <li x-text="err"></li>
                                    </template>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Company Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="company_name" 
                                x-model="companyAddForm.company_name" 
                                placeholder="e.g. ABC Transport (Pvt) Ltd"
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                        </div>

                        <!-- Registration Number -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Company Registration Number
                            </label>
                            <input 
                                type="text" 
                                name="registration_number" 
                                x-model="companyAddForm.registration_number" 
                                placeholder="e.g. PV-12345"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-mono"
                            >
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Company Status
                            </label>
                            <select 
                                name="status" 
                                x-model="companyAddForm.status" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Contact Person -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="customer_name" 
                                x-model="companyAddForm.customer_name" 
                                placeholder="e.g. Nimal Perera"
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                        </div>

                        <!-- Mobile Number -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="phone" 
                                x-model="companyAddForm.phone" 
                                placeholder="e.g. 0771234567"
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                        </div>

                        <!-- Email Address -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Email Address <span class="text-gray-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                x-model="companyAddForm.email" 
                                placeholder="e.g. billing@abctransport.lk"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Company Address -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Company Address
                            </label>
                            <input 
                                type="text" 
                                name="address" 
                                x-model="companyAddForm.address" 
                                placeholder="e.g. 125, Baseline Road, Colombo 09"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Credit Limit -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Credit Limit (LKR)
                            </label>
                            <input 
                                type="number" 
                                step="0.01"
                                min="0"
                                name="credit_limit" 
                                x-model="companyAddForm.credit_limit" 
                                placeholder="e.g. 100000"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-semibold"
                            >
                        </div>

                        <!-- Payment Terms -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Payment Terms
                            </label>
                            <select 
                                name="payment_terms" 
                                x-model="companyAddForm.payment_terms" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                                <option value="immediate">Immediate Payment</option>
                                <option value="7_days">7 Days</option>
                                <option value="15_days">15 Days</option>
                                <option value="30_days">30 Days</option>
                                <option value="custom">Custom Days</option>
                            </select>
                        </div>

                        <!-- Custom Days (if custom) -->
                        <div x-show="companyAddForm.payment_terms === 'custom'" class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Custom Payment Terms (Days)
                            </label>
                            <input 
                                type="number" 
                                min="1" 
                                max="365"
                                name="custom_days" 
                                x-model="companyAddForm.custom_days" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Notes <span class="text-gray-400 font-normal lowercase">(optional)</span>
                            </label>
                            <textarea 
                                name="notes" 
                                x-model="companyAddForm.notes" 
                                rows="2"
                                placeholder="Additional notes about company or credit agreement..."
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            ></textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showAddCompanyModal = false"
                            class="px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="isSubmittingAddCompany"
                            class="px-5 py-2 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-sm transition cursor-pointer flex items-center space-x-1.5"
                        >
                            <template x-if="isSubmittingAddCompany">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span>Save Company</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 2: EDIT COMPANY                                    -->
    <!-- ======================================================== -->
    <div 
        x-show="showEditCompanyModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak
        @keydown.escape.window="showEditCompanyModal = false"
    >
        <!-- Pure Dim Backdrop (NO backdrop-blur, NO overlapping dialog) -->
        <div 
            x-show="showEditCompanyModal" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showEditCompanyModal = false"
            class="fixed inset-0 bg-gray-900/60 transition-opacity"
            aria-hidden="true"
        ></div>

        <!-- Center Container -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <!-- Modal Dialog Panel (Sharp, elevated with relative z-10 and pointer-events-auto) -->
            <div 
                x-show="showEditCompanyModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative z-10 pointer-events-auto transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-200 flex flex-col max-h-[90vh]"
            >
                <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between shrink-0">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                        <h3 class="text-base font-bold tracking-tight">Edit Company Profile</h3>
                    </div>
                    <button type="button" @click="showEditCompanyModal = false" class="text-gray-300 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form 
                    :action="'{{ url('operations/company-vehicles/companies') }}/' + companyForm.id" 
                    method="POST" 
                    @submit.prevent="submitEditCompany($event)"
                    class="overflow-y-auto flex-1 p-6 space-y-4"
                >
                    @csrf
                    @method('PUT')

                    <!-- Inline Validation Errors Container -->
                    <template x-if="Object.keys(companyErrors).length > 0">
                        <div class="p-3.5 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-red-800">
                                <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Please fix the following errors:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 ml-1">
                                <template x-for="(errList, field) in companyErrors" :key="field">
                                    <template x-for="err in errList" :key="err">
                                        <li x-text="err"></li>
                                    </template>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Company Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="company_name" 
                                x-model="companyForm.company_name" 
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Registration Number
                            </label>
                            <input 
                                type="text" 
                                name="registration_number" 
                                x-model="companyForm.registration_number" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-mono"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Company Status
                            </label>
                            <select 
                                name="status" 
                                x-model="companyForm.status" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-semibold"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="customer_name" 
                                x-model="companyForm.customer_name" 
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="phone" 
                                x-model="companyForm.phone" 
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Email Address
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                x-model="companyForm.email" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Company Address
                            </label>
                            <input 
                                type="text" 
                                name="address" 
                                x-model="companyForm.address" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Credit Limit (LKR)
                            </label>
                            <input 
                                type="number" 
                                step="0.01"
                                min="0"
                                name="credit_limit" 
                                x-model="companyForm.credit_limit" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-semibold"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Payment Terms
                            </label>
                            <select 
                                name="payment_terms" 
                                x-model="companyForm.payment_terms" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                                <option value="immediate">Immediate Payment</option>
                                <option value="7_days">7 Days</option>
                                <option value="15_days">15 Days</option>
                                <option value="30_days">30 Days</option>
                                <option value="custom">Custom Days</option>
                            </select>
                        </div>

                        <!-- Custom Days (if custom) -->
                        <div x-show="companyForm.payment_terms === 'custom'" class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Custom Payment Terms (Days)
                            </label>
                            <input 
                                type="number" 
                                min="1" 
                                max="365"
                                name="custom_days" 
                                x-model="companyForm.custom_days" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Notes <span class="text-gray-400 font-normal lowercase">(optional)</span>
                            </label>
                            <textarea 
                                name="notes" 
                                x-model="companyForm.notes" 
                                rows="2"
                                placeholder="Additional notes about company or credit agreement..."
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            ></textarea>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showEditCompanyModal = false"
                            class="px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="isSubmittingCompany"
                            class="px-5 py-2 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-sm transition cursor-pointer flex items-center space-x-1.5"
                        >
                            <template x-if="isSubmittingCompany">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 3: ADD VEHICLE UNDER SELECTED COMPANY              -->
    <!-- ======================================================== -->
    <div 
        x-show="showAddVehicleModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak
        @keydown.escape.window="showAddVehicleModal = false"
    >
        <!-- Pure Dim Backdrop (NO backdrop-blur, NO overlapping dialog) -->
        <div 
            x-show="showAddVehicleModal" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showAddVehicleModal = false"
            class="fixed inset-0 bg-gray-900/60 transition-opacity"
            aria-hidden="true"
        ></div>

        <!-- Center Container -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <!-- Modal Dialog Panel (Sharp, elevated with relative z-10 and pointer-events-auto) -->
            <div 
                x-show="showAddVehicleModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative z-10 pointer-events-auto transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-200 flex flex-col max-h-[90vh]"
            >
                <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between shrink-0">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <h3 class="text-base font-bold tracking-tight">Register Vehicle for Company</h3>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5">
                            Company: <strong class="text-white" x-text="vehicleForm.company_name"></strong>
                        </p>
                    </div>
                    <button type="button" @click="showAddVehicleModal = false" class="text-gray-300 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('operations.company-vehicles.vehicles.store') }}" method="POST" class="overflow-y-auto flex-1 p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="company_id" :value="vehicleForm.company_id">

                    <!-- Vehicle Plate Number with Live Normalization & Duplicate Detection -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Vehicle Registration Number <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="vehicle_number" 
                                x-model="vehicleForm.vehicle_number" 
                                @blur="checkVehiclePlate()"
                                @keydown.enter.prevent="checkVehiclePlate()"
                                placeholder="e.g. CAA-1234 or WP-LZ-6677"
                                required
                                class="w-full px-3.5 py-2.5 text-base font-bold uppercase tracking-wider border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <template x-if="checkingVehicle">
                                    <svg class="animate-spin h-4 w-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                            </div>
                        </div>

                        <!-- Live Vehicle Duplicate & Ownership Transfer Feedback -->
                        <template x-if="vehicleCheckResult && vehicleCheckResult.exists">
                            <div class="mt-2.5">
                                <template x-if="vehicleCheckResult.is_same_company">
                                    <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-800 font-semibold flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        This vehicle is already registered under this company!
                                    </div>
                                </template>

                                <template x-if="!vehicleCheckResult.is_same_company">
                                    <div class="p-3 rounded-lg bg-blue-50 border border-blue-300 text-xs text-blue-900 space-y-2">
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 mr-1.5 text-blue-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="font-bold">Existing vehicle detected in system!</p>
                                                <p class="text-[11px] text-blue-800 mt-0.5">
                                                    Current Owner: <strong x-text="vehicleCheckResult.current_owner_name"></strong>. Historical service records and billing ownership will be preserved.
                                                </p>
                                            </div>
                                        </div>
                                        <label class="flex items-center space-x-2 pt-1 font-bold cursor-pointer text-blue-950">
                                            <input 
                                                type="checkbox" 
                                                name="transfer_confirmed" 
                                                value="1" 
                                                x-model="vehicleForm.transfer_confirmed"
                                                class="w-4 h-4 text-[#0B1849] rounded border-gray-300 focus:ring-[#0B1849]"
                                            >
                                            <span>Authorize transfer of vehicle to <span x-text="vehicleForm.company_name"></span></span>
                                        </label>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Vehicle Type -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Type <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="vehicle_type_id" 
                                x-model="vehicleForm.vehicle_type_id" 
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                                @foreach($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Brand -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Brand
                            </label>
                            <input 
                                type="text" 
                                name="brand" 
                                x-model="vehicleForm.brand" 
                                placeholder="e.g. Toyota, Isuzu, Tata"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Model -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Model
                            </label>
                            <input 
                                type="text" 
                                name="model" 
                                x-model="vehicleForm.model" 
                                placeholder="e.g. HiAce, KDH, D-Max"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Driver Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Driver Name <span class="text-gray-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input 
                                type="text" 
                                name="driver_name" 
                                x-model="vehicleForm.driver_name" 
                                placeholder="e.g. Kasun"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Driver Mobile -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Driver Mobile <span class="text-gray-400 font-normal lowercase">(optional)</span>
                            </label>
                            <input 
                                type="text" 
                                name="driver_mobile" 
                                x-model="vehicleForm.driver_mobile" 
                                placeholder="e.g. 0777123456"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-mono"
                            >
                        </div>

                        <!-- Current Mileage -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Current Mileage (km)
                            </label>
                            <input 
                                type="number" 
                                min="0"
                                name="current_mileage" 
                                x-model="vehicleForm.current_mileage" 
                                placeholder="e.g. 45000"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Next Service Mileage -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Next Service Mileage (km)
                            </label>
                            <input 
                                type="number" 
                                min="0"
                                name="next_service_mileage" 
                                x-model="vehicleForm.next_service_mileage" 
                                placeholder="e.g. 50000"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                            <div class="mt-1 flex items-center space-x-1">
                                <button type="button" @click="addMileage(5000)" class="text-[10px] px-1.5 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-semibold cursor-pointer">+5,000 km</button>
                                <button type="button" @click="addMileage(10000)" class="text-[10px] px-1.5 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded font-semibold cursor-pointer">+10,000 km</button>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Notes / Requirements
                            </label>
                            <input 
                                type="text" 
                                name="notes" 
                                x-model="vehicleForm.notes" 
                                placeholder="e.g. Assigned to Kandy branch deliveries"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showAddVehicleModal = false"
                            class="px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-sm transition cursor-pointer"
                        >
                            Save Vehicle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 4: EDIT VEHICLE DETAILS                            -->
    <!-- ======================================================== -->
    <div 
        x-show="showEditVehicleModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak
        @keydown.escape.window="showEditVehicleModal = false"
    >
        <!-- Pure Dim Backdrop (NO backdrop-blur, NO overlapping dialog) -->
        <div 
            x-show="showEditVehicleModal" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showEditVehicleModal = false"
            class="fixed inset-0 bg-gray-900/60 transition-opacity"
            aria-hidden="true"
        ></div>

        <!-- Center Container -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <!-- Modal Dialog Panel (Sharp, elevated with relative z-10 and pointer-events-auto) -->
            <div 
                x-show="showEditVehicleModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative z-10 pointer-events-auto transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-200 flex flex-col max-h-[90vh]"
            >
                <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between shrink-0">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                            <h3 class="text-base font-bold tracking-tight">Edit Vehicle Details</h3>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5">
                            Vehicle: <strong class="text-white font-mono" x-text="vehicleForm.vehicle_number"></strong>
                        </p>
                    </div>
                    <button type="button" @click="showEditVehicleModal = false" class="text-gray-300 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form 
                    :action="'{{ url('operations/company-vehicles/vehicles') }}/' + vehicleForm.id" 
                    method="POST" 
                    @submit.prevent="submitEditVehicle($event)"
                    class="overflow-y-auto flex-1 p-6 space-y-4"
                >
                    @csrf
                    @method('PUT')

                    <!-- Validation Errors Box Inside Edit Vehicle Modal -->
                    <template x-if="Object.keys(vehicleErrors).length > 0">
                        <div class="p-3.5 rounded-lg bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1">
                            <div class="font-bold flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Please resolve the following errors:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 pl-5">
                                <template x-for="(errList, field) in vehicleErrors" :key="field">
                                    <template x-for="msg in errList" :key="msg">
                                        <li x-text="msg"></li>
                                    </template>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Vehicle Registration Number -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Registration Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="vehicle_number" 
                                x-model="vehicleForm.vehicle_number" 
                                required
                                class="w-full px-3.5 py-2.5 text-sm font-bold uppercase tracking-wider border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-mono"
                            >
                        </div>

                        <!-- Vehicle Type -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Type <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="vehicle_type_id" 
                                x-model="vehicleForm.vehicle_type_id" 
                                required
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-medium"
                            >
                                @foreach($vehicleTypes as $vt)
                                    <option value="{{ $vt->id }}">{{ $vt->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Vehicle Brand -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Brand
                            </label>
                            <input 
                                type="text" 
                                name="brand" 
                                x-model="vehicleForm.brand" 
                                placeholder="e.g. Toyota, Isuzu"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Vehicle Model -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Model
                            </label>
                            <input 
                                type="text" 
                                name="model" 
                                x-model="vehicleForm.model" 
                                placeholder="e.g. HiAce, KDH"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Vehicle Year -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Vehicle Year
                            </label>
                            <input 
                                type="number" 
                                name="year_of_manufacture" 
                                x-model="vehicleForm.year_of_manufacture" 
                                placeholder="e.g. 2018"
                                min="1950"
                                max="{{ date('Y') + 1 }}"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Driver Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Driver Name
                            </label>
                            <input 
                                type="text" 
                                name="driver_name" 
                                x-model="vehicleForm.driver_name" 
                                placeholder="e.g. Kasun Perera"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Driver Mobile Number -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Driver Mobile Number
                            </label>
                            <input 
                                type="text" 
                                name="driver_mobile" 
                                x-model="vehicleForm.driver_mobile" 
                                placeholder="e.g. 0771234567"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white font-mono"
                            >
                        </div>

                        <!-- Current Mileage -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Current Mileage (km)
                            </label>
                            <input 
                                type="number" 
                                min="0"
                                name="current_mileage" 
                                x-model="vehicleForm.current_mileage" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Next Service Mileage -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Next Service Mileage (km)
                            </label>
                            <input 
                                type="number" 
                                min="0"
                                name="next_service_mileage" 
                                x-model="vehicleForm.next_service_mileage" 
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Notes
                            </label>
                            <input 
                                type="text" 
                                name="notes" 
                                x-model="vehicleForm.notes" 
                                placeholder="e.g. Assigned to express logistics"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:outline-none bg-white"
                            >
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                        <button 
                            type="button" 
                            @click="showEditVehicleModal = false"
                            class="px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            :disabled="isSubmittingVehicle"
                            class="px-5 py-2 text-xs font-bold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-sm transition cursor-pointer flex items-center space-x-1.5"
                        >
                            <template x-if="isSubmittingVehicle">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 5: VEHICLE SERVICE / JOB HISTORY MODAL             -->
    <!-- ======================================================== -->
    <div 
        x-show="showHistoryModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak
        @keydown.escape.window="showHistoryModal = false"
    >
        <!-- Pure Dim Backdrop (NO backdrop-blur, NO overlapping dialog) -->
        <div 
            x-show="showHistoryModal" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showHistoryModal = false"
            class="fixed inset-0 bg-gray-900/60 transition-opacity"
            aria-hidden="true"
        ></div>

        <!-- Center Container -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <!-- Modal Dialog Panel (Sharp, elevated with relative z-10 and pointer-events-auto) -->
            <div 
                x-show="showHistoryModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative z-10 pointer-events-auto transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-5xl border border-gray-200 flex flex-col max-h-[90vh]"
            >
                <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between shrink-0">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <h3 class="text-base font-bold tracking-tight">
                                Vehicle Job History – <span class="font-mono text-emerald-300" x-text="historyVehicle ? historyVehicle.vehicle_number : ''"></span>
                            </h3>
                        </div>
                        <template x-if="historyVehicle">
                            <p class="text-xs text-slate-300 mt-0.5">
                                Type: <span class="text-white" x-text="historyVehicle.type"></span> | 
                                Model: <span class="text-white" x-text="historyVehicle.make_model"></span> | 
                                Driver: <span class="text-white" x-text="historyVehicle.driver_name"></span> | 
                                Current Mileage: <span class="text-white" x-text="historyVehicle.current_mileage"></span>
                            </p>
                        </template>
                    </div>
                    <button type="button" @click="showHistoryModal = false" class="text-gray-300 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 p-6">
                    <template x-if="loadingHistory">
                        <div class="py-12 text-center text-gray-500">
                            <svg class="animate-spin h-8 w-8 text-[#0B1849] mx-auto" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p class="text-xs font-semibold text-gray-600 mt-2">Loading vehicle job history...</p>
                        </div>
                    </template>

                    <template x-if="!loadingHistory && historyRecords.length === 0">
                        <div class="py-12 text-center text-gray-500">
                            <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-sm font-bold text-gray-700">No job history found for this vehicle.</p>
                            <p class="text-xs text-gray-400 mt-0.5">There are no job card records registered for this vehicle.</p>
                        </div>
                    </template>

                    <template x-if="!loadingHistory && historyRecords.length > 0">
                        <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-2xs">
                            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                                <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] tracking-wider sticky top-0">
                                    <tr>
                                        <th class="px-3.5 py-3">Service Date</th>
                                        <th class="px-3.5 py-3">Job Number</th>
                                        <th class="px-3.5 py-3">Invoice Number</th>
                                        <th class="px-3.5 py-3">Services Completed</th>
                                        <th class="px-3.5 py-3">Products Used</th>
                                        <th class="px-3.5 py-3">Technician</th>
                                        <th class="px-3.5 py-3">Mileage</th>
                                        <th class="px-3.5 py-3 text-right">Total</th>
                                        <th class="px-3.5 py-3 text-center">Status</th>
                                        <th class="px-3.5 py-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-gray-800">
                                    <template x-for="rec in historyRecords" :key="rec.id">
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="px-3.5 py-2.5 font-semibold text-gray-900 whitespace-nowrap" x-text="rec.date"></td>
                                            <td class="px-3.5 py-2.5 whitespace-nowrap font-bold font-mono text-[#0B1849]" x-text="rec.job_number"></td>
                                            <td class="px-3.5 py-2.5 whitespace-nowrap font-mono text-gray-700" x-text="rec.invoice_number"></td>
                                            <td class="px-3.5 py-2.5 max-w-[180px]">
                                                <template x-if="rec.services && rec.services.length > 0">
                                                    <div class="space-y-0.5">
                                                        <template x-for="s in rec.services" :key="s">
                                                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] bg-slate-100 text-slate-800 mr-1 mb-0.5 font-medium" x-text="s"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="!rec.services || rec.services.length === 0">
                                                    <span class="text-gray-400 italic">-</span>
                                                </template>
                                            </td>
                                            <td class="px-3.5 py-2.5 max-w-[160px]">
                                                <template x-if="rec.products && rec.products.length > 0">
                                                    <div class="space-y-0.5">
                                                        <template x-for="p in rec.products" :key="p">
                                                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] bg-blue-50 text-blue-800 mr-1 mb-0.5 font-medium" x-text="p"></span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="!rec.products || rec.products.length === 0">
                                                    <span class="text-gray-400 italic">-</span>
                                                </template>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-gray-700 whitespace-nowrap" x-text="rec.technician"></td>
                                            <td class="px-3.5 py-2.5 font-semibold text-gray-900 whitespace-nowrap" x-text="rec.mileage"></td>
                                            <td class="px-3.5 py-2.5 text-right font-bold text-gray-900 whitespace-nowrap" x-text="'LKR ' + rec.grand_total.toLocaleString('en-US', {minimumFractionDigits: 2})"></td>
                                            <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                <span 
                                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                    :class="{
                                                        'bg-emerald-100 text-emerald-800': rec.status_raw === 'completed' || rec.status_raw === 'delivered',
                                                        'bg-blue-100 text-blue-800': rec.status_raw === 'in_progress',
                                                        'bg-amber-100 text-amber-800': rec.status_raw === 'pending',
                                                        'bg-gray-100 text-gray-800': rec.status_raw === 'cancelled'
                                                    }"
                                                    x-text="rec.status"
                                                ></span>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-right whitespace-nowrap">
                                                <a 
                                                    :href="rec.view_url" 
                                                    target="_blank" 
                                                    class="inline-flex items-center px-2.5 py-1 bg-[#0B1849] hover:bg-[#15286e] text-white text-[11px] font-bold rounded-md transition shadow-2xs cursor-pointer"
                                                >
                                                    View Job Card
                                                </a>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end shrink-0">
                    <button 
                        type="button" 
                        @click="showHistoryModal = false"
                        class="px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- MODAL 6: COMPANY INVOICES & STATEMENT MODAL              -->
    <!-- ======================================================== -->
    <div 
        x-show="showInvoicesModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak
        @keydown.escape.window="showInvoicesModal = false"
    >
        <!-- Pure Dim Backdrop (NO backdrop-blur, NO overlapping dialog) -->
        <div 
            x-show="showInvoicesModal" 
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showInvoicesModal = false"
            class="fixed inset-0 bg-gray-900/60 transition-opacity"
            aria-hidden="true"
        ></div>

        <!-- Center Container -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0 pointer-events-none">
            <!-- Modal Dialog Panel (Sharp, elevated with relative z-10 and pointer-events-auto) -->
            <div 
                x-show="showInvoicesModal"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.stop
                class="relative z-10 pointer-events-auto transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-5xl border border-gray-200 flex flex-col max-h-[90vh]"
            >
                <div class="px-6 py-4 bg-[#0B1849] text-white flex items-center justify-between shrink-0">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            <h3 class="text-base font-bold tracking-tight">Company Invoices & Billing Account</h3>
                        </div>
                        <p class="text-xs text-slate-300 mt-0.5">
                            Company: <strong class="text-white">{{ $selectedCompany?->company_name }}</strong>
                        </p>
                    </div>
                    <button type="button" @click="showInvoicesModal = false" class="text-gray-300 hover:text-white cursor-pointer">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 p-6 space-y-4">
                    <!-- Filters Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-gray-50 p-3.5 rounded-xl border border-gray-200 text-xs">
                        <div>
                            <label class="block font-bold uppercase text-[10px] text-gray-600 mb-1">Date From</label>
                            <input 
                                type="date" 
                                x-model="invoiceFilters.date_from" 
                                @change="loadCompanyInvoices()"
                                class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white"
                            >
                        </div>
                        <div>
                            <label class="block font-bold uppercase text-[10px] text-gray-600 mb-1">Date To</label>
                            <input 
                                type="date" 
                                x-model="invoiceFilters.date_to" 
                                @change="loadCompanyInvoices()"
                                class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white"
                            >
                        </div>
                        <div>
                            <label class="block font-bold uppercase text-[10px] text-gray-600 mb-1">Vehicle</label>
                            <select 
                                x-model="invoiceFilters.vehicle_id" 
                                @change="loadCompanyInvoices()"
                                class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white"
                            >
                                <option value="">All Vehicles</option>
                                @if($selectedCompany)
                                    @foreach($selectedCompany->vehicles as $veh)
                                        <option value="{{ $veh->id }}">{{ $veh->vehicle_number }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold uppercase text-[10px] text-gray-600 mb-1">Payment Status</label>
                            <select 
                                x-model="invoiceFilters.status" 
                                @change="loadCompanyInvoices()"
                                class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded bg-white"
                            >
                                <option value="all">All Invoices</option>
                                <option value="unpaid">Unpaid Only</option>
                                <option value="partially_paid">Partially Paid</option>
                                <option value="paid">Fully Paid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Summary Stats Bar -->
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-blue-50 border border-blue-200 p-2.5 rounded-lg text-xs">
                            <span class="text-blue-700 font-semibold uppercase text-[10px] block">Total Invoices</span>
                            <span class="text-sm font-black text-blue-900 mt-0.5 block" x-text="'LKR ' + (invoicesSummary.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                        </div>
                        <div class="bg-emerald-50 border border-emerald-200 p-2.5 rounded-lg text-xs">
                            <span class="text-emerald-700 font-semibold uppercase text-[10px] block">Amount Paid</span>
                            <span class="text-sm font-black text-emerald-900 mt-0.5 block" x-text="'LKR ' + (invoicesSummary.total_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                        </div>
                        <div class="bg-rose-50 border border-rose-200 p-2.5 rounded-lg text-xs">
                            <span class="text-rose-700 font-semibold uppercase text-[10px] block">Total Outstanding</span>
                            <span class="text-sm font-black text-rose-900 mt-0.5 block" x-text="'LKR ' + (invoicesSummary.total_balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2})"></span>
                        </div>
                    </div>

                    <!-- Invoices Table -->
                    <div class="overflow-x-auto max-h-96 thin-scrollbar border border-gray-200 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px] tracking-wider sticky top-0">
                                <tr>
                                    <th class="px-3.5 py-2.5">Date</th>
                                    <th class="px-3.5 py-2.5">Invoice #</th>
                                    <th class="px-3.5 py-2.5">Vehicle</th>
                                    <th class="px-3.5 py-2.5">Job #</th>
                                    <th class="px-3.5 py-2.5 text-right">Amount</th>
                                    <th class="px-3.5 py-2.5 text-right">Paid</th>
                                    <th class="px-3.5 py-2.5 text-right">Balance</th>
                                    <th class="px-3.5 py-2.5">Status</th>
                                    <th class="px-3.5 py-2.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-800">
                                <template x-if="loadingInvoices">
                                    <tr>
                                        <td colspan="9" class="p-8 text-center text-gray-500">
                                            Loading invoices...
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="!loadingInvoices && invoicesList.length === 0">
                                    <tr>
                                        <td colspan="9" class="p-8 text-center text-gray-500">
                                            No invoices found matching these filters.
                                        </td>
                                    </tr>
                                </template>

                                <template x-for="inv in invoicesList" :key="inv.id">
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-3.5 py-2 font-medium text-gray-900 whitespace-nowrap" x-text="inv.invoice_date"></td>
                                        <td class="px-3.5 py-2 font-mono font-bold text-[#0B1849] whitespace-nowrap" x-text="inv.invoice_number"></td>
                                        <td class="px-3.5 py-2 font-semibold text-gray-800 whitespace-nowrap" x-text="inv.vehicle_number"></td>
                                        <td class="px-3.5 py-2 font-mono text-gray-600 whitespace-nowrap" x-text="inv.job_number"></td>
                                        <td class="px-3.5 py-2 text-right font-semibold text-gray-900 whitespace-nowrap" x-text="'LKR ' + inv.grand_total.toLocaleString('en-US', {minimumFractionDigits: 2})"></td>
                                        <td class="px-3.5 py-2 text-right text-emerald-700 whitespace-nowrap" x-text="'LKR ' + inv.amount_paid.toLocaleString('en-US', {minimumFractionDigits: 2})"></td>
                                        <td class="px-3.5 py-2 text-right font-bold whitespace-nowrap" :class="inv.balance_amount > 0 ? 'text-rose-600' : 'text-gray-400'" x-text="'LKR ' + inv.balance_amount.toLocaleString('en-US', {minimumFractionDigits: 2})"></td>
                                        <td class="px-3.5 py-2 whitespace-nowrap">
                                            <span 
                                                class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                :class="{
                                                    'bg-emerald-100 text-emerald-800': inv.status === 'paid',
                                                    'bg-amber-100 text-amber-800': inv.status === 'partially_paid',
                                                    'bg-rose-100 text-rose-800': inv.status === 'unpaid'
                                                }"
                                                x-text="inv.status_label"
                                            ></span>
                                        </td>
                                        <td class="px-3.5 py-2 text-right whitespace-nowrap">
                                            <a 
                                                :href="inv.view_url" 
                                                target="_blank" 
                                                class="inline-flex items-center px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-[10px] font-semibold rounded"
                                            >
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 shrink-0">
                    @if($selectedCompany)
                        <a 
                            href="{{ route('operations.company-vehicles.companies.statement', $selectedCompany) }}"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-xs rounded-lg transition shadow-xs cursor-pointer"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Generate Company Statement
                        </a>
                    @endif

                    <button 
                        type="button" 
                        @click="showInvoicesModal = false"
                        class="px-4 py-2 text-xs font-bold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
