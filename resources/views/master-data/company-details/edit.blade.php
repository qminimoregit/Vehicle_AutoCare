@extends('layouts.app')

@section('title', 'Company Details')

@section('content')
    <div 
        class="max-w-5xl mx-auto"
        x-data="{
            activeTab: '{{ $errors->has('branch_code') || $errors->has('branch_name') ? 'branches' : ($activeTab ?? 'info') }}',
            addModalOpen: {{ ($errors->has('branch_code') || $errors->has('branch_name')) && !old('_method') ? 'true' : 'false' }},
            editModalOpen: {{ ($errors->has('branch_code') || $errors->has('branch_name')) && old('_method') === 'PUT' ? 'true' : 'false' }},
            viewModalOpen: false,
            currentBranch: {
                id: null,
                branch_code: '',
                branch_name: '',
                address: '',
                phone: '',
                email: '',
                manager_id: '',
                manager_name: '',
                is_main_branch: false,
                is_active: true,
                assigned_staff_ids: [],
                assigned_staff_names: []
            },
            editUrl: '',

            openAddModal() {
                this.addModalOpen = true;
            },

            openEditModal(branch) {
                this.currentBranch = Object.assign({}, branch);
                this.editUrl = '{{ url('master-data/branches') }}/' + branch.id;
                this.editModalOpen = true;
            },

            openViewModal(branch) {
                this.currentBranch = Object.assign({}, branch);
                this.viewModalOpen = true;
            }
        }"
    >
        <!-- Page Header -->
        <x-page-header 
            title="Company Details" 
            description="Manage the auto care center's company information and multiple branches."
        />

        @if(!($canEdit ?? Auth::user()->isAdmin()))
            <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-amber-800 text-xs">
                <strong>View Only Mode:</strong> You are logged in as staff. Administrator access is required to modify company details or manage branches.
            </div>
        @endif

        <!-- Tab Navigation Buttons -->
        <div class="flex border-b border-gray-200 mb-6 space-x-2 sm:space-x-4">
            <!-- Tab 1: Company Information -->
            <button 
                type="button"
                @click="activeTab = 'info'"
                :class="activeTab === 'info' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                class="pb-3 px-3 sm:px-4 border-b-2 text-sm transition flex items-center space-x-2 cursor-pointer focus:outline-none"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>Company Information</span>
            </button>

            <!-- Tab 2: Branches -->
            <button 
                type="button"
                @click="activeTab = 'branches'"
                :class="activeTab === 'branches' ? 'border-[#0B1849] text-[#0B1849] font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                class="pb-3 px-3 sm:px-4 border-b-2 text-sm transition flex items-center space-x-2 cursor-pointer focus:outline-none"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span>Branches</span>
                <span class="ml-1.5 px-2 py-0.5 text-[11px] rounded-full bg-slate-100 text-slate-700 font-semibold">{{ $branches->count() }}</span>
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: COMPANY INFORMATION -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'info'" class="space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-6 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Company Information</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Primary business identity and registration details for the main company.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('master-data.company-details.update') }}" enctype="multipart/form-data" class="space-y-6" novalidate>
                    @csrf
                    @method('PUT')

                    <!-- 1. Company Name -->
                    <div>
                        <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Company Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="company_name" 
                                id="company_name" 
                                required 
                                @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                                value="{{ old('company_name', $company->company_name) }}" 
                                placeholder="Enter Company Name"
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('company_name') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        <x-input-error for="company_name" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 2. Registration Number -->
                        <div>
                            <label for="registration_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Business Registration Number
                            </label>
                            <div class="mt-1.5">
                                <input 
                                    type="text" 
                                    name="registration_number" 
                                    id="registration_number" 
                                    @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                                    value="{{ old('registration_number', $company->registration_number) }}" 
                                    placeholder="Enter Business Registration Number"
                                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono disabled:bg-gray-100 disabled:text-gray-500 @error('registration_number') border-rose-400 bg-rose-50/30 @enderror"
                                />
                            </div>
                            <x-input-error for="registration_number" />
                        </div>

                        <!-- 3. Tax / VAT Number -->
                        <div>
                            <label for="tax_number" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                VAT / Tax Number <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <div class="mt-1.5">
                                <input 
                                    type="text" 
                                    name="tax_number" 
                                    id="tax_number" 
                                    @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                                    value="{{ old('tax_number', $company->tax_number) }}" 
                                    placeholder="e.g. VAT-123456789-000"
                                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono disabled:bg-gray-100 disabled:text-gray-500 @error('tax_number') border-rose-400 bg-rose-50/30 @enderror"
                                />
                            </div>
                            <x-input-error for="tax_number" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- 4. Contact Number -->
                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Contact Number
                            </label>
                            <div class="mt-1.5">
                                <input 
                                    type="text" 
                                    name="phone" 
                                    id="phone" 
                                    @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                                    value="{{ old('phone', $company->phone) }}" 
                                    placeholder="e.g. +94 11 234 5678"
                                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('phone') border-rose-400 bg-rose-50/30 @enderror"
                                />
                            </div>
                            <x-input-error for="phone" />
                        </div>

                        <!-- 5. Email -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Email Address
                            </label>
                            <div class="mt-1.5">
                                <input 
                                    type="email" 
                                    name="email" 
                                    id="email" 
                                    @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                                    value="{{ old('email', $company->email) }}" 
                                    placeholder="e.g. info@autocare.local"
                                    class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('email') border-rose-400 bg-rose-50/30 @enderror"
                                />
                            </div>
                            <x-input-error for="email" />
                        </div>
                    </div>

                    <!-- 6. Address -->
                    <div>
                        <label for="address" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Physical Address
                        </label>
                        <div class="mt-1.5">
                            <textarea 
                                name="address" 
                                id="address" 
                                rows="3" 
                                @disabled(!($canEdit ?? Auth::user()->isAdmin()))
                                placeholder="Enter Head Office / Main Workshop Address"
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] disabled:bg-gray-100 disabled:text-gray-500 @error('address') border-rose-400 bg-rose-50/30 @enderror"
                            >{{ old('address', $company->address) }}</textarea>
                        </div>
                        <x-input-error for="address" />
                    </div>

                    <!-- 7. Company Logo -->
                    <div>
                        <label for="logo" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                            Company Logo
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <!-- Current Logo Preview -->
                            <div class="w-24 h-24 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if($company->logo_path)
                                    <img src="{{ asset('storage/' . $company->logo_path) }}" alt="Company Logo" class="max-h-full max-w-full object-contain p-2" />
                                @else
                                    <div class="text-center p-2 text-gray-400">
                                        <svg class="mx-auto h-7 w-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-[10px] mt-1 block">No Logo</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload Control -->
                            @if($canEdit ?? Auth::user()->isAdmin())
                                <div class="flex-1">
                                    <input 
                                        type="file" 
                                        name="logo" 
                                        id="logo" 
                                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#0B1849] file:text-white hover:file:bg-[#15286e] cursor-pointer"
                                    />
                                    <p class="mt-1.5 text-xs text-gray-400">Supported: PNG, JPG, JPEG, SVG, WEBP (Max 2MB).</p>
                                    <x-input-error for="logo" />
                                </div>
                            @else
                                <div class="text-xs text-gray-400">
                                    Logo uploads restricted to administrators.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- 8. Authorized Signature -->
                    <div>
                        <label for="signature" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                            Authorized Signature
                        </label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <!-- Current Signature Preview -->
                            <div class="w-48 h-20 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0 p-2">
                                @if($company->signature_path)
                                    <img src="{{ asset('storage/' . $company->signature_path) }}" alt="Authorized Signature" class="max-h-full max-w-full object-contain" />
                                @else
                                    <div class="text-center p-2 text-gray-400">
                                        <svg class="mx-auto h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        <span class="text-[10px] mt-0.5 block">No Signature Uploaded</span>
                                        <span class="text-[9px] text-gray-400 block">(Blank line will appear on receipts)</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Upload Control -->
                            @if($canEdit ?? Auth::user()->isAdmin())
                                <div class="flex-1 space-y-2">
                                    <input 
                                        type="file" 
                                        name="signature" 
                                        id="signature" 
                                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                        class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#0B1849] file:text-white hover:file:bg-[#15286e] cursor-pointer"
                                    />
                                    <p class="text-xs text-gray-400">Upload an authorized signature image for receipts. Supported: PNG, JPG, JPEG, SVG, WEBP (Max 2MB).</p>
                                    @if($company->signature_path)
                                        <label class="inline-flex items-center text-xs text-rose-600 hover:text-rose-800 cursor-pointer mt-1">
                                            <input type="checkbox" name="remove_signature" value="1" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500 mr-1.5">
                                            <span>Remove existing signature image</span>
                                        </label>
                                    @endif
                                    <x-input-error for="signature" />
                                </div>
                            @else
                                <div class="text-xs text-gray-400">
                                    Signature uploads restricted to authorized administrators.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Button: [Save Changes] -->
                    @if($canEdit ?? Auth::user()->isAdmin())
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end">
                            <button 
                                type="submit" 
                                class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                            >
                                Save Changes
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: BRANCHES MANAGEMENT -->
        <!-- ========================================== -->
        <div x-show="activeTab === 'branches'" class="space-y-6" style="display: none;">
            <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
                <!-- Branches Header Bar -->
                <div class="p-4 sm:p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Branch Management</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Manage auto care center branches, locations, managers, and operational statuses.</p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <!-- Search Input -->
                        <form method="GET" action="{{ route('master-data.company-details.edit') }}" class="relative flex-1 sm:w-64">
                            <input type="hidden" name="tab" value="branches">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Search branches..." 
                                class="w-full pl-9 pr-3.5 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                            />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            @if(request('search'))
                                <a href="{{ route('master-data.company-details.edit', ['tab' => 'branches']) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 text-xs">
                                    ✕
                                </a>
                            @endif
                        </form>

                        <!-- [+ Add Branch] Button (Admin only) -->
                        @if($canEdit ?? Auth::user()->isAdmin())
                            <button 
                                type="button" 
                                @click="openAddModal()" 
                                class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#0B1849] shrink-0"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Add Branch</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Branches Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                        <thead class="bg-gray-50 text-gray-600 uppercase tracking-wider font-semibold">
                            <tr>
                                <th scope="col" class="px-4 sm:px-6 py-3">Branch Name</th>
                                <th scope="col" class="px-4 sm:px-6 py-3">Location</th>
                                <th scope="col" class="px-4 sm:px-6 py-3">Phone</th>
                                <th scope="col" class="px-4 sm:px-6 py-3">Manager</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-center">Status</th>
                                <th scope="col" class="px-4 sm:px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($branches as $branch)
                                @php
                                    $branchData = [
                                        'id' => $branch->id,
                                        'branch_code' => $branch->branch_code,
                                        'branch_name' => $branch->branch_name,
                                        'address' => $branch->address ?? '',
                                        'phone' => $branch->phone ?? '',
                                        'email' => $branch->email ?? '',
                                        'manager_id' => $branch->manager_id ?? '',
                                        'manager_name' => $branch->manager ? $branch->manager->name : 'None',
                                        'is_main_branch' => (bool) $branch->is_main_branch,
                                        'is_active' => (bool) $branch->is_active,
                                        'assigned_staff_ids' => $branch->users->pluck('id')->toArray(),
                                        'assigned_staff_names' => $branch->users->pluck('name')->toArray(),
                                        'created_at' => $branch->created_at ? $branch->created_at->format('Y-m-d') : '',
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition">
                                    <!-- Branch Name & Code & Main Badge -->
                                    <td class="px-4 sm:px-6 py-3.5">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-bold text-gray-900">{{ $branch->branch_name }}</span>
                                            @if($branch->is_main_branch)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    ★ Main Branch
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-mono text-gray-500 mt-0.5">
                                            Code: {{ $branch->branch_code }}
                                        </div>
                                    </td>

                                    <!-- Location / Address -->
                                    <td class="px-4 sm:px-6 py-3.5 text-gray-600 max-w-xs truncate" title="{{ $branch->address }}">
                                        {{ $branch->address ?: '—' }}
                                    </td>

                                    <!-- Phone & Email -->
                                    <td class="px-4 sm:px-6 py-3.5 text-gray-700">
                                        <div>{{ $branch->phone ?: '—' }}</div>
                                        @if($branch->email)
                                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $branch->email }}</div>
                                        @endif
                                    </td>

                                    <!-- Manager -->
                                    <td class="px-4 sm:px-6 py-3.5 text-gray-700">
                                        @if($branch->manager)
                                            <span class="inline-flex items-center font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                                {{ $branch->manager->name }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-4 sm:px-6 py-3.5 text-center">
                                        @if($branch->is_active)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 sm:px-6 py-3.5 text-right space-x-1 whitespace-nowrap">
                                        <!-- View Button -->
                                        <button 
                                            type="button" 
                                            @click="openViewModal({{ json_encode($branchData) }})"
                                            class="inline-flex items-center px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded transition cursor-pointer"
                                            title="View Branch"
                                        >
                                            View
                                        </button>

                                        @if($canEdit ?? Auth::user()->isAdmin())
                                            <!-- Edit Button -->
                                            <button 
                                                type="button" 
                                                @click="openEditModal({{ json_encode($branchData) }})"
                                                class="inline-flex items-center px-2 py-1 text-xs font-semibold text-[#0B1849] bg-blue-50 hover:bg-blue-100 rounded transition cursor-pointer"
                                                title="Edit Branch"
                                            >
                                                Edit
                                            </button>

                                            <!-- Set Main Branch Button -->
                                            @if(!$branch->is_main_branch)
                                                <form method="POST" action="{{ route('master-data.branches.set-main', $branch) }}" class="inline" onsubmit="return confirm('Make {{ $branch->branch_name }} the Main Branch?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button 
                                                        type="submit" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded transition cursor-pointer"
                                                        title="Set as Main Branch"
                                                    >
                                                        Set Main
                                                    </button>
                                                </form>

                                                <!-- Activate / Deactivate Toggle -->
                                                <form method="POST" action="{{ route('master-data.branches.toggle-status', $branch) }}" class="inline" onsubmit="return confirm('Change status for {{ $branch->branch_name }}?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button 
                                                        type="submit" 
                                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold {{ $branch->is_active ? 'text-rose-600 bg-rose-50 hover:bg-rose-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }} rounded transition cursor-pointer"
                                                        title="{{ $branch->is_active ? 'Deactivate Branch' : 'Activate Branch' }}"
                                                    >
                                                        {{ $branch->is_active ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-xs">
                                        @if(request('search'))
                                            No branches found matching "<strong>{{ request('search') }}</strong>".
                                        @else
                                            No branches registered yet.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL 1: ADD BRANCH -->
        <!-- ========================================== -->
        <div 
            x-show="addModalOpen" 
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.outside="addModalOpen = false" 
                class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-bold text-gray-900">Add New Branch</h3>
                    <button type="button" @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600 text-base font-bold">✕</button>
                </div>

                <form method="POST" action="{{ route('master-data.branches.store') }}" class="p-6 space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Branch Code -->
                        <div>
                            <label for="add_branch_code" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Branch Code <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="branch_code" 
                                id="add_branch_code" 
                                required 
                                value="{{ old('branch_code') }}" 
                                placeholder="e.g. BR-002"
                                class="mt-1 block w-full px-3 py-2 text-xs font-mono uppercase border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] @error('branch_code') border-rose-400 bg-rose-50/30 @enderror"
                            />
                            <x-input-error for="branch_code" />
                        </div>

                        <!-- Branch Name -->
                        <div>
                            <label for="add_branch_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Branch Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="branch_name" 
                                id="add_branch_name" 
                                required 
                                value="{{ old('branch_name') }}" 
                                placeholder="e.g. Kandy Branch"
                                class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] @error('branch_name') border-rose-400 bg-rose-50/30 @enderror"
                            />
                            <x-input-error for="branch_name" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="add_address" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Address
                        </label>
                        <textarea 
                            name="address" 
                            id="add_address" 
                            rows="2" 
                            placeholder="Enter branch workshop location / address"
                            class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                        >{{ old('address') }}</textarea>
                        <x-input-error for="address" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Contact Number -->
                        <div>
                            <label for="add_phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Contact Number
                            </label>
                            <input 
                                type="text" 
                                name="phone" 
                                id="add_phone" 
                                value="{{ old('phone') }}" 
                                placeholder="e.g. +94 81 223 4567"
                                class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                            />
                            <x-input-error for="phone" />
                        </div>

                        <!-- Email (Optional) -->
                        <div>
                            <label for="add_email" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Email <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="add_email" 
                                value="{{ old('email') }}" 
                                placeholder="branch@autocare.local"
                                class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                            />
                            <x-input-error for="email" />
                        </div>
                    </div>

                    <!-- Branch Manager (Optional) -->
                    <div>
                        <label for="add_manager_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Branch Manager <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <select 
                            name="manager_id" 
                            id="add_manager_id" 
                            class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                        >
                            <option value="">-- No Branch Manager Assigned --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('manager_id') == $user->id)>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="manager_id" />
                    </div>

                    <!-- Assigned Staff Checkboxes -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Assigned Staff Members
                        </label>
                        <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-2.5 space-y-1 bg-gray-50/50">
                            @foreach($users as $user)
                                <label class="flex items-center space-x-2 text-xs text-gray-700 hover:text-gray-900 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="assigned_staff[]" 
                                        value="{{ $user->id }}"
                                        class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                    />
                                    <span>{{ $user->name }} <span class="text-gray-400 text-[10px]">({{ ucfirst($user->role) }})</span></span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Staff members can only switch to branches they have been assigned to.</p>
                    </div>

                    <!-- Checkboxes: Main Branch & Status -->
                    <div class="pt-2 border-t border-gray-100 space-y-2">
                        <label class="flex items-center space-x-2 text-xs font-semibold text-gray-800 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_main_branch" 
                                value="1" 
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                            />
                            <span>Set as Main Branch</span>
                        </label>

                        <label class="flex items-center space-x-2 text-xs font-semibold text-gray-800 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1" 
                                checked 
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                            />
                            <span>Branch is Active</span>
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <button 
                            type="button" 
                            @click="addModalOpen = false" 
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition"
                        >
                            Save Branch
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL 2: EDIT BRANCH -->
        <!-- ========================================== -->
        <div 
            x-show="editModalOpen" 
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.outside="editModalOpen = false" 
                class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-lg w-full overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-bold text-gray-900">
                        Edit Branch: <span x-text="currentBranch.branch_name"></span>
                    </h3>
                    <button type="button" @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600 text-base font-bold">✕</button>
                </div>

                <form method="POST" :action="editUrl" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Branch Code -->
                        <div>
                            <label for="edit_branch_code" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Branch Code <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="branch_code" 
                                id="edit_branch_code" 
                                required 
                                x-model="currentBranch.branch_code"
                                class="mt-1 block w-full px-3 py-2 text-xs font-mono uppercase border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                            />
                            <x-input-error for="branch_code" />
                        </div>

                        <!-- Branch Name -->
                        <div>
                            <label for="edit_branch_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Branch Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="branch_name" 
                                id="edit_branch_name" 
                                required 
                                x-model="currentBranch.branch_name"
                                class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                            />
                            <x-input-error for="branch_name" />
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="edit_address" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Address
                        </label>
                        <textarea 
                            name="address" 
                            id="edit_address" 
                            rows="2" 
                            x-model="currentBranch.address"
                            class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                        ></textarea>
                        <x-input-error for="address" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Contact Number -->
                        <div>
                            <label for="edit_phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Contact Number
                            </label>
                            <input 
                                type="text" 
                                name="phone" 
                                id="edit_phone" 
                                x-model="currentBranch.phone"
                                class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                            />
                            <x-input-error for="phone" />
                        </div>

                        <!-- Email (Optional) -->
                        <div>
                            <label for="edit_email" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Email <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="edit_email" 
                                x-model="currentBranch.email"
                                class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                            />
                            <x-input-error for="email" />
                        </div>
                    </div>

                    <!-- Branch Manager (Optional) -->
                    <div>
                        <label for="edit_manager_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Branch Manager <span class="text-gray-400 font-normal">(Optional)</span>
                        </label>
                        <select 
                            name="manager_id" 
                            id="edit_manager_id" 
                            x-model="currentBranch.manager_id"
                            class="mt-1 block w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849]"
                        >
                            <option value="">-- No Branch Manager Assigned --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="manager_id" />
                    </div>

                    <!-- Assigned Staff Checkboxes -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                            Assigned Staff Members
                        </label>
                        <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-2.5 space-y-1 bg-gray-50/50">
                            @foreach($users as $user)
                                <label class="flex items-center space-x-2 text-xs text-gray-700 hover:text-gray-900 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        name="assigned_staff[]" 
                                        value="{{ $user->id }}"
                                        :checked="currentBranch.assigned_staff_ids && currentBranch.assigned_staff_ids.includes({{ $user->id }})"
                                        class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                                    />
                                    <span>{{ $user->name }} <span class="text-gray-400 text-[10px]">({{ ucfirst($user->role) }})</span></span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Checkboxes: Main Branch & Status -->
                    <div class="pt-2 border-t border-gray-100 space-y-2">
                        <label class="flex items-center space-x-2 text-xs font-semibold text-gray-800 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_main_branch" 
                                value="1" 
                                x-model="currentBranch.is_main_branch"
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                            />
                            <span>Set as Main Branch</span>
                        </label>

                        <label class="flex items-center space-x-2 text-xs font-semibold text-gray-800 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="is_active" 
                                value="1" 
                                x-model="currentBranch.is_active"
                                class="rounded border-gray-300 text-[#0B1849] focus:ring-[#0B1849]"
                            />
                            <span>Branch is Active</span>
                        </label>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <button 
                            type="button" 
                            @click="editModalOpen = false" 
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition"
                        >
                            Update Branch
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL 3: VIEW BRANCH DETAILS -->
        <!-- ========================================== -->
        <div 
            x-show="viewModalOpen" 
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-xs flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.outside="viewModalOpen = false" 
                class="bg-white rounded-xl shadow-2xl border border-gray-200 max-w-md w-full overflow-hidden"
            >
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-bold text-gray-900">Branch Details</h3>
                    <button type="button" @click="viewModalOpen = false" class="text-gray-400 hover:text-gray-600 text-base font-bold">✕</button>
                </div>

                <div class="p-6 space-y-4 text-xs">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Branch Name</div>
                        <div class="text-sm font-bold text-gray-900 mt-0.5 flex items-center space-x-2">
                            <span x-text="currentBranch.branch_name"></span>
                            <template x-if="currentBranch.is_main_branch">
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    ★ Main Branch
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Branch Code</div>
                            <div class="font-mono text-gray-900 mt-0.5" x-text="currentBranch.branch_code"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Operational Status</div>
                            <div class="mt-0.5">
                                <template x-if="currentBranch.is_active">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        ● Active
                                    </span>
                                </template>
                                <template x-if="!currentBranch.is_active">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                        ● Inactive
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Physical Location</div>
                        <div class="text-gray-800 mt-0.5" x-text="currentBranch.address || 'No address specified.'"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Contact Number</div>
                            <div class="text-gray-800 mt-0.5" x-text="currentBranch.phone || '—'"></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Email Address</div>
                            <div class="text-gray-800 mt-0.5" x-text="currentBranch.email || '—'"></div>
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Branch Manager</div>
                        <div class="text-gray-800 mt-0.5 font-medium" x-text="currentBranch.manager_name || 'None'"></div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Assigned Staff</div>
                        <div class="mt-1 flex flex-wrap gap-1.5">
                            <template x-if="currentBranch.assigned_staff_names && currentBranch.assigned_staff_names.length > 0">
                                <template x-for="name in currentBranch.assigned_staff_names" :key="name">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-medium" x-text="name"></span>
                                </template>
                            </template>
                            <template x-if="!currentBranch.assigned_staff_names || currentBranch.assigned_staff_names.length === 0">
                                <span class="text-gray-400">No staff explicitly assigned.</span>
                            </template>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button 
                            type="button" 
                            @click="viewModalOpen = false" 
                            class="px-4 py-2 text-xs font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg transition"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
