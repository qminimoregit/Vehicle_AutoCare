@extends('layouts.app')

@section('title', 'Vehicle Registration')

@section('content')
<div 
    x-data="{
        vehicleNumber: '{{ old('vehicle_number', $initialSearch) }}',
        customerName: '{{ old('customer_name') }}',
        phone: '{{ old('phone') }}',
        email: '{{ old('email') }}',
        vehicleTypeId: '{{ old('vehicle_type_id') }}',
        currentMileage: '{{ old('current_mileage') }}',
        nextServiceMileage: '{{ old('next_service_mileage') }}',
        customerType: '{{ old('customer_type', 'individual') }}',
        companyName: '{{ old('company_name') }}',
        address: '{{ old('address') }}',
        makeModel: '{{ old('make_model') }}',
        brand: '{{ old('brand') }}',
        model: '{{ old('model') }}',
        driverName: '{{ old('driver_name') }}',
        driverMobile: '{{ old('driver_mobile') }}',
        customerNotes: '{{ old('customer_notes') }}',
        showMoreDetails: false,
        
        // Search State
        isSearching: false,
        searchPerformed: false,
        vehicleExists: false,
        searchMessage: '',
        ongoingJobs: [],
        serviceHistory: [],

        // Company / Fleet Details
        isCompanyCustomer: false,
        hasCreditAccount: false,
        availableCredit: 0,
        
        // Vehicle Types with Carpet Configurations
        vehicleTypes: {{ Js::from($vehicleTypes->mapWithKeys(fn($t) => [
            (string)$t->id => [
                'id' => $t->id,
                'name' => $t->name,
                'enable_carpets' => (bool)$t->enable_carpets,
                'categories' => $t->carpetCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
            ]
        ])) }},

        // Selected Carpet Details: { [categoryId]: quantity }
        selectedCarpets: {},

        hasCarpetSupport() {
            return !!(this.vehicleTypeId && this.vehicleTypes[this.vehicleTypeId]?.enable_carpets);
        },

        currentCarpetCategories() {
            if (!this.hasCarpetSupport()) return [];
            return this.vehicleTypes[this.vehicleTypeId]?.categories || [];
        },

        isCarpetSelected(catId) {
            return this.selectedCarpets.hasOwnProperty(catId) && this.selectedCarpets[catId] > 0;
        },

        toggleCarpet(catId) {
            if (this.isCarpetSelected(catId)) {
                let copy = Object.assign({}, this.selectedCarpets);
                delete copy[catId];
                this.selectedCarpets = copy;
            } else {
                let copy = Object.assign({}, this.selectedCarpets);
                copy[catId] = 1;
                this.selectedCarpets = copy;
            }
        },

        setVehicleType(typeId) {
            this.vehicleTypeId = String(typeId);
            this.syncCarpetsForVehicleType();
        },

        syncCarpetsForVehicleType() {
            if (!this.hasCarpetSupport()) {
                this.selectedCarpets = {};
                return;
            }
            const allowedIds = this.currentCarpetCategories().map(c => Number(c.id));
            let copy = Object.assign({}, this.selectedCarpets);
            let changed = false;
            for (const catId of Object.keys(copy)) {
                if (!allowedIds.includes(Number(catId))) {
                    delete copy[catId];
                    changed = true;
                }
            }
            if (changed) {
                this.selectedCarpets = copy;
            }
        },

        // Mileage shortcuts
        addMileage(km) {
            let current = parseInt(this.currentMileage) || 0;
            if (current > 0) {
                this.nextServiceMileage = current + km;
            } else {
                alert('Please enter Current Mileage first to use quick suggestions.');
            }
        },

        // Vehicle Search via API
        async searchVehicle() {
            let num = this.vehicleNumber.trim();
            if (!num) {
                this.searchMessage = 'Please enter a vehicle registration number.';
                return;
            }
            this.isSearching = true;
            this.searchMessage = '';
            
            try {
                let response = await fetch('{{ route('operations.vehicles.search') }}?number=' + encodeURIComponent(num));
                let data = await response.json();
                
                this.searchPerformed = true;
                this.vehicleExists = data.exists;

                if (data.exists) {
                    this.vehicleNumber = data.vehicle.vehicle_number;
                    this.vehicleTypeId = data.vehicle.vehicle_type_id ? String(data.vehicle.vehicle_type_id) : '';
                    this.makeModel = data.vehicle.make_model || '';
                    this.currentMileage = data.vehicle.current_mileage || '';
                    this.nextServiceMileage = data.vehicle.next_service_mileage || '';
                    this.customerNotes = data.vehicle.notes || '';
                    this.driverName = data.vehicle.driver_name || '';
                    this.driverMobile = data.vehicle.driver_mobile || '';
                    this.brand = data.vehicle.brand || '';
                    this.model = data.vehicle.model || '';

                    this.customerName = data.customer.customer_name || '';
                    this.phone = data.customer.phone || '';
                    this.email = data.customer.email || '';
                    this.customerType = data.customer.customer_type || 'individual';
                    this.companyName = data.customer.company_name || '';
                    this.address = data.customer.address || '';
                    this.isCompanyCustomer = !!data.customer.is_company;
                    this.hasCreditAccount = !!data.customer.has_credit_account;
                    this.availableCredit = parseFloat(data.customer.available_credit) || 0;

                    this.ongoingJobs = data.ongoing_jobs || [];
                    this.serviceHistory = data.service_history || [];
                    this.searchMessage = data.message || 'Existing Vehicle Found – Customer Details Loaded';
                    
                    this.syncCarpetsForVehicleType();
                } else {
                    this.vehicleExists = false;
                    this.customerName = '';
                    this.phone = '';
                    this.email = '';
                    this.customerType = 'individual';
                    this.companyName = '';
                    this.address = '';
                    this.makeModel = '';
                    this.brand = '';
                    this.model = '';
                    this.driverName = '';
                    this.driverMobile = '';
                    this.currentMileage = '';
                    this.nextServiceMileage = '';
                    this.customerNotes = '';
                    this.isCompanyCustomer = false;
                    this.hasCreditAccount = false;
                    this.availableCredit = 0;
                    this.ongoingJobs = [];
                    this.serviceHistory = [];
                    this.selectedCarpets = {};
                    this.searchMessage = data.message || 'New Vehicle – Enter Customer Details';
                }
            } catch (err) {
                console.error(err);
                this.searchMessage = 'Error connecting to server. Please try again.';
            } finally {
                this.isSearching = false;
            }
        },

        init() {
            this.$watch('vehicleTypeId', () => this.syncCarpetsForVehicleType());
            if (this.vehicleNumber.trim().length >= 3) {
                this.searchVehicle();
            }
        }
    }"
    class="max-w-6xl mx-auto space-y-6"
>
    <!-- Page Header -->
    <x-page-header 
        title="Vehicle Registration" 
        description="Search existing vehicle records or register a new vehicle to initiate a service job."
    >
        <x-slot:actions>
            <a 
                href="{{ route('operations.job-cards.index') }}" 
                class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-50 shadow-xs transition"
            >
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                View Job Cards
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- 1. VEHICLE LOOKUP & REGISTRATION FORM -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/60 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#0B1849]"></span>
                <h2 class="text-base font-bold text-gray-900">Vehicle & Customer Information</h2>
            </div>
            <span class="text-xs text-gray-500 font-medium hidden sm:inline">Normalized search automatically matches spaces & hyphens</span>
        </div>

        <form action="{{ route('operations.vehicles.store') }}" method="POST" class="p-5 sm:p-6 space-y-6">
            @csrf

            <!-- Vehicle Search Bar Banner -->
            <div class="bg-[#EEF1FA]/60 border border-[#0B1849]/15 rounded-xl p-4 sm:p-5">
                <label for="vehicle_number" class="block text-xs font-bold uppercase tracking-wider text-[#0B1849] mb-1.5">
                    Vehicle Registration Number <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="vehicle_number" 
                            id="vehicle_number"
                            x-model="vehicleNumber"
                            @keydown.enter.prevent="searchVehicle()"
                            placeholder="e.g. CAA-1234, CAA 1234, caa1234"
                            class="block w-full pl-11 pr-4 py-3 text-base sm:text-lg font-bold uppercase text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white tracking-wider"
                            required
                        >
                    </div>

                    <button 
                        type="button" 
                        @click="searchVehicle()"
                        :disabled="isSearching"
                        class="inline-flex items-center justify-center px-6 py-3 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-sm sm:text-base rounded-lg shadow-xs transition cursor-pointer disabled:opacity-50 min-h-[44px]"
                    >
                        <template x-if="isSearching">
                            <span class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Searching...
                            </span>
                        </template>
                        <template x-if="!isSearching">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Search Vehicle
                            </span>
                        </template>
                    </button>
                </div>

                <!-- Feedback status message -->
                <div x-show="searchPerformed" class="mt-3 flex flex-wrap items-center gap-2" style="display: none;">
                    <template x-if="vehicleExists">
                        <span class="inline-flex items-center text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-md border border-emerald-200 text-xs sm:text-sm font-bold">
                            <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="searchMessage"></span>
                        </span>
                    </template>
                    <template x-if="!vehicleExists">
                        <span class="inline-flex items-center text-blue-800 bg-blue-50 px-3 py-1.5 rounded-md border border-blue-200 text-xs sm:text-sm font-bold">
                            <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span x-text="searchMessage"></span>
                        </span>
                    </template>

                    <!-- Company Fleet Indicator if applicable -->
                    <template x-if="vehicleExists && isCompanyCustomer">
                        <span class="inline-flex items-center text-indigo-800 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200 text-xs font-semibold">
                            <svg class="w-3.5 h-3.5 mr-1 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Fleet Account: <strong class="ml-1" x-text="companyName || customerName"></strong>
                            <template x-if="hasCreditAccount">
                                <span class="ml-1.5 text-[11px] text-indigo-600 font-bold">(Credit Active)</span>
                            </template>
                        </span>
                    </template>
                </div>
            </div>

            <!-- Ongoing Active Jobs Warning Banner (if any) -->
            <div 
                x-show="ongoingJobs.length > 0" 
                class="bg-amber-50 border border-amber-300 rounded-xl p-4 text-amber-900"
                style="display: none;"
            >
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 mr-2.5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-amber-900">Active Job Already In Progress!</h4>
                        <p class="text-xs text-amber-800 mt-0.5">
                            This vehicle currently has an ongoing job. Avoid creating duplicate job cards. You can open and continue the existing job below:
                        </p>
                        <div class="mt-2.5 space-y-2">
                            <template x-for="job in ongoingJobs" :key="job.id">
                                <div class="bg-white/80 p-2.5 rounded-lg border border-amber-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                                    <div>
                                        <span class="font-bold text-gray-900" x-text="job.job_number"></span>
                                        <span class="mx-1 text-gray-400">•</span>
                                        <span class="text-gray-600" x-text="'Date: ' + job.date"></span>
                                        <span class="mx-1 text-gray-400">•</span>
                                        <span class="px-2 py-0.5 rounded-full font-semibold text-[11px] bg-amber-100 text-amber-800" x-text="job.status_label"></span>
                                    </div>
                                    <a 
                                        :href="job.view_url" 
                                        class="inline-flex items-center px-3 py-1 bg-[#0B1849] text-white text-xs font-semibold rounded-md hover:bg-[#15286e] transition"
                                    >
                                        Open Active Job
                                        <svg class="w-3.5 h-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer & Vehicle Main Fields Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                <!-- Customer Name -->
                <div>
                    <label for="customer_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        <span x-text="isCompanyCustomer ? 'Company Contact Person' : 'Customer Name'"></span> <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="customer_name" 
                        id="customer_name"
                        x-model="customerName"
                        placeholder="e.g. Kamal Perera"
                        class="block w-full py-2.5 px-3.5 text-sm sm:text-base text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                        required
                    >
                    @error('customer_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mobile Number -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        <span x-text="isCompanyCustomer ? 'Company Phone / Mobile' : 'Mobile Number'"></span> <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone"
                        x-model="phone"
                        placeholder="e.g. 0771234567"
                        class="block w-full py-2.5 px-3.5 text-sm sm:text-base text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                        required
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address (Optional) -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Email Address <span class="text-gray-400 font-normal lowercase">(optional)</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        x-model="email"
                        placeholder="e.g. customer@example.com"
                        class="block w-full py-2.5 px-3.5 text-sm sm:text-base text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Company Driver & Fleet Details (When company vehicle is detected) -->
            <div x-show="isCompanyCustomer" class="bg-indigo-50/70 border border-indigo-200 rounded-xl p-4 sm:p-5 space-y-3" style="display: none;">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0B1849]"></span>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">
                            Company Vehicle Driver Details
                        </h4>
                    </div>
                    <span class="text-[11px] text-indigo-700 font-medium">Company billing contact is protected. Enter driver info below.</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="driver_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Driver Name <span class="text-gray-400 font-normal lowercase">(optional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="driver_name" 
                            id="driver_name"
                            x-model="driverName"
                            placeholder="e.g. Kasun Fernando"
                            class="block w-full py-2.5 px-3.5 text-sm sm:text-base text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                        >
                    </div>

                    <div>
                        <label for="driver_mobile" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                            Driver Mobile <span class="text-gray-400 font-normal lowercase">(optional)</span>
                        </label>
                        <input 
                            type="text" 
                            name="driver_mobile" 
                            id="driver_mobile"
                            x-model="driverMobile"
                            placeholder="e.g. 0777654321"
                            class="block w-full py-2.5 px-3.5 text-sm sm:text-base text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                        >
                    </div>
                </div>
            </div>

            <!-- 2. VEHICLE TYPE SELECTION -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Vehicle Type <span class="text-red-500">*</span>
                    </label>
                    <span class="text-[11px] sm:text-xs text-gray-500 hidden sm:inline">Service prices & carpet options adjust according to vehicle type</span>
                </div>

                <!-- Hidden Input for Form Submission -->
                <input type="hidden" name="vehicle_type_id" x-model="vehicleTypeId" required>

                <!-- Touch-friendly Vehicle Type Cards (All Screen Sizes) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2.5 sm:gap-3">
                    @foreach($vehicleTypes as $type)
                        <button 
                            type="button" 
                            @click="setVehicleType('{{ $type->id }}')"
                            class="p-2.5 sm:p-3 rounded-xl border text-center transition flex flex-col items-center justify-center space-y-1.5 sm:space-y-2 cursor-pointer focus:outline-none min-h-[85px] active:scale-[0.98]"
                            :class="vehicleTypeId === '{{ $type->id }}' ? 'border-[#0B1849] bg-[#EEF1FA] text-[#0B1849] ring-2 ring-[#0B1849]/20 font-bold shadow-xs' : 'border-gray-200 bg-white hover:bg-slate-50 text-gray-700 hover:border-gray-300'"
                        >
                            @if($type->image)
                                <img src="{{ asset('storage/' . $type->image) }}" alt="{{ $type->name }}" class="h-8 sm:h-9 w-auto object-contain">
                            @else
                                <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            @endif
                            <span class="text-xs font-semibold block leading-tight text-center">{{ $type->name }}</span>
                            @if($type->enable_carpets)
                                <span class="text-[9px] sm:text-[10px] text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded font-normal">Carpets</span>
                            @endif
                        </button>
                    @endforeach
                </div>
                @error('vehicle_type_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror

                <!-- 2B. CARPET DETAILS (OPTIONAL) - DIRECTLY BELOW VEHICLE TYPE -->
                <div 
                    x-show="hasCarpetSupport() && currentCarpetCategories().length > 0" 
                    class="mt-4 bg-slate-50 border border-slate-200 rounded-xl p-4 sm:p-5 space-y-3 transition-all"
                    style="display: none;"
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-[#0B1849]"></span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#0B1849]">
                                CARPET DETAILS <span class="text-gray-400 font-normal lowercase">(optional)</span>
                            </h4>
                        </div>
                        <span class="text-[11px] text-gray-500 font-medium">Tick received carpets and specify quantity</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-1">
                        <template x-for="cat in currentCarpetCategories()" :key="cat.id">
                            <div 
                                class="flex items-center justify-between p-2.5 rounded-lg border transition-all"
                                :class="isCarpetSelected(cat.id) ? 'bg-[#EEF1FA]/70 border-[#0B1849]/30 ring-1 ring-[#0B1849]/20' : 'bg-white border-gray-200 hover:border-gray-300'"
                            >
                                <label class="flex items-center space-x-2.5 cursor-pointer select-none text-xs font-bold text-gray-800 flex-1 mr-2">
                                    <input 
                                        type="checkbox" 
                                        :checked="isCarpetSelected(cat.id)"
                                        @change="toggleCarpet(cat.id)"
                                        class="w-4 h-4 text-[#0B1849] rounded border-gray-300 focus:ring-[#0B1849] cursor-pointer"
                                    >
                                    <span x-text="cat.name"></span>
                                </label>

                                <!-- Quantity Input (Only displayed when category is selected) -->
                                <div x-show="isCarpetSelected(cat.id)" class="flex items-center space-x-1.5 shrink-0" style="display: none;">
                                    <span class="text-[11px] font-bold text-gray-600">Qty:</span>
                                    <input 
                                        type="number" 
                                        min="1" 
                                        step="1"
                                        x-model.number="selectedCarpets[cat.id]"
                                        :name="'carpets[' + cat.id + '][quantity]'"
                                        class="w-16 py-1 px-2 text-xs font-black text-center text-gray-900 border border-gray-300 rounded-md bg-white focus:outline-none focus:ring-1 focus:ring-[#0B1849] min-h-[30px]"
                                    >
                                    <input type="hidden" :name="'carpets[' + cat.id + '][category_id]'" :value="cat.id">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- 3. MILEAGE MANAGEMENT -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                <!-- Current Mileage -->
                <div>
                    <label for="current_mileage" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Current Mileage (km)
                    </label>
                    <input 
                        type="number" 
                        name="current_mileage" 
                        id="current_mileage"
                        x-model="currentMileage"
                        placeholder="e.g. 50000"
                        class="block w-full py-2.5 px-3.5 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                    >
                    <p class="mt-1 text-[11px] text-gray-500">Record accurate mileage before starting work</p>
                </div>

                <!-- Next Service Mileage -->
                <div>
                    <label for="next_service_mileage" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                        Next Service Mileage (km)
                    </label>
                    <input 
                        type="number" 
                        name="next_service_mileage" 
                        id="next_service_mileage"
                        x-model="nextServiceMileage"
                        placeholder="e.g. 55000"
                        class="block w-full py-2.5 px-3.5 text-sm font-semibold text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white min-h-[44px]"
                    >

                    <!-- Quick Suggestion Shortcuts -->
                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-bold text-gray-500 mr-1">Quick Add:</span>
                        <button 
                            type="button" 
                            @click="addMileage(3500)"
                            class="px-2.5 py-1 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md border border-gray-200 transition min-h-[32px] cursor-pointer"
                        >
                            +3,500 km
                        </button>
                        <button 
                            type="button" 
                            @click="addMileage(5000)"
                            class="px-2.5 py-1 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md border border-gray-200 transition min-h-[32px] cursor-pointer"
                        >
                            +5,000 km
                        </button>
                        <button 
                            type="button" 
                            @click="addMileage(10000)"
                            class="px-2.5 py-1 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md border border-gray-200 transition min-h-[32px] cursor-pointer"
                        >
                            +10,000 km
                        </button>
                        <button 
                            type="button" 
                            @click="addMileage(40000)"
                            class="px-2.5 py-1 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md border border-gray-200 transition min-h-[32px] cursor-pointer"
                        >
                            +40,000 km
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4. COLLAPSIBLE MORE DETAILS (OPTIONAL FIELDS) -->
            <div class="border-t border-gray-100 pt-3">
                <button 
                    type="button" 
                    @click="showMoreDetails = !showMoreDetails"
                    class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-[#0B1849] hover:underline cursor-pointer focus:outline-none min-h-[36px]"
                >
                    <svg 
                        :class="showMoreDetails ? 'rotate-90' : ''" 
                        class="w-4 h-4 mr-1.5 transform transition-transform duration-150 text-gray-400" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span x-text="showMoreDetails ? 'Hide Optional Details' : '+ Show More Details (Make/Model, Address, Fleet/Company, Notes)'"></span>
                </button>

                <div x-show="showMoreDetails" class="mt-4 space-y-4 pt-1" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Make / Model -->
                        <div>
                            <label for="make_model" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                Make / Model
                            </label>
                            <input 
                                type="text" 
                                name="make_model" 
                                id="make_model"
                                x-model="makeModel"
                                placeholder="e.g. Toyota Prius, Honda Civic"
                                class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                            <input type="hidden" name="brand" :value="brand">
                            <input type="hidden" name="model" :value="model">
                        </div>

                        <!-- Customer Type (Individual vs Company) -->
                        <div>
                            <label for="customer_type" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                Customer Type
                            </label>
                            <select 
                                name="customer_type" 
                                id="customer_type"
                                x-model="customerType"
                                class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                                <option value="individual">Individual Customer</option>
                                <option value="company">Company / Fleet Account</option>
                            </select>
                        </div>

                        <!-- Company Name (if Company) -->
                        <div x-show="customerType === 'company'">
                            <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                Company Name
                            </label>
                            <input 
                                type="text" 
                                name="company_name" 
                                id="company_name"
                                x-model="companyName"
                                placeholder="e.g. ABC Logistics (Pvt) Ltd"
                                class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Customer Address -->
                        <div>
                            <label for="address" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                Customer Address
                            </label>
                            <input 
                                type="text" 
                                name="address" 
                                id="address"
                                x-model="address"
                                placeholder="e.g. 12/A, Galle Road, Colombo 03"
                                class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>

                        <!-- Customer Notes -->
                        <div>
                            <label for="customer_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1">
                                Customer / Service Notes
                            </label>
                            <input 
                                type="text" 
                                name="customer_notes" 
                                id="customer_notes"
                                x-model="customerNotes"
                                placeholder="e.g. Check strange rattling noise from front suspension"
                                class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0B1849] bg-white min-h-[44px]"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Action Button -->
            <div class="border-t border-gray-100 pt-5 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span class="text-xs text-gray-500 font-medium order-2 sm:order-1 text-center sm:text-left">
                    Saves customer & vehicle details, validates active jobs, and initializes Job Card.
                </span>
                
                <button 
                    type="submit" 
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-[#0B1849] hover:bg-[#15286e] text-white font-bold text-base rounded-xl shadow-md transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849] min-h-[48px] order-1 sm:order-2"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span x-text="vehicleExists ? 'Confirm Details & Start Job' : 'Register & Start Job'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- 5. REAL PREVIOUS SERVICE HISTORY UNDERNEATH -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-slate-50/60 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900">
                    Previous Service History
                </h3>
            </div>
            <template x-if="vehicleExists">
                <span class="text-xs font-semibold text-[#0B1849] bg-[#EEF1FA] px-2.5 py-0.5 rounded-full" x-text="serviceHistory.length + ' Completed Records'"></span>
            </template>
        </div>

        <div class="p-5">
            <!-- State 1: When vehicle has completed history -->
            <template x-if="vehicleExists && serviceHistory.length > 0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs sm:text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Job Number</th>
                                <th class="px-4 py-3">Services Performed</th>
                                <th class="px-4 py-3">Mileage</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Invoice</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-800">
                            <template x-for="item in serviceHistory" :key="item.id">
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-3 font-medium text-gray-900" x-text="item.date"></td>
                                    <td class="px-4 py-3 font-bold text-[#0B1849]" x-text="item.job_number"></td>
                                    <td class="px-4 py-3 text-gray-700" x-text="item.services"></td>
                                    <td class="px-4 py-3 font-semibold text-gray-900" x-text="item.mileage"></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800" x-text="item.status"></span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-600" x-text="item.invoice_number"></td>
                                    <td class="px-4 py-3 text-right">
                                        <a 
                                            :href="item.view_url" 
                                            class="inline-flex items-center px-3 py-1.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-semibold rounded-md shadow-xs transition"
                                        >
                                            View Job
                                        </a>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </template>

            <!-- State 2: Existing vehicle but has no completed past jobs yet -->
            <template x-if="vehicleExists && serviceHistory.length === 0">
                <div class="py-8 text-center text-gray-500">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="mt-2 text-sm font-semibold text-gray-700">No previous completed service history.</p>
                    <p class="text-xs text-gray-400 mt-0.5">This vehicle is registered in the system but has no completed job cards.</p>
                </div>
            </template>

            <!-- State 3: Brand new vehicle or no vehicle searched yet -->
            <template x-if="!vehicleExists">
                <div class="py-8 text-center text-gray-500">
                    <svg class="mx-auto h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="mt-2 text-sm font-semibold text-gray-700">No previous service history.</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <template x-if="searchPerformed">
                            <span>This is a new vehicle. Enter the details above to register and start its first job.</span>
                        </template>
                        <template x-if="!searchPerformed">
                            <span>Search for an existing vehicle registration number above to inspect its real service history.</span>
                        </template>
                    </p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
