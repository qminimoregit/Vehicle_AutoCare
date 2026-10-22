@extends('layouts.app')

@section('title', 'Printer Settings')

@section('content')
<div x-data="printerSettingsComponent()" x-init="initComponent()" class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-xs">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-xl bg-[#0B1849] text-white flex items-center justify-center shadow-xs">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">Printer Settings</h1>
                    <p class="text-xs text-gray-500 font-medium">Manage two-printer routing (Bill Printer &amp; Job Card Printer) per branch and workstation.</p>
                </div>
            </div>
        </div>

        <!-- Status Badges -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- QZ Tray Status -->
            <div class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold border transition"
                 :class="qzConnected ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-rose-50 text-rose-800 border-rose-200'">
                <span class="w-2 h-2 rounded-full mr-2"
                      :class="qzConnected ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></span>
                <span x-text="qzConnected ? 'QZ Tray: Online' : 'QZ Tray: Offline'"></span>
            </div>

            <!-- Current Workstation Identifier -->
            <div class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Workstation:</span>
                <span class="ml-1 font-bold text-[#0B1849]" x-text="currentWorkstation"></span>
            </div>

            <!-- Refresh / Connect Button -->
            <button 
                type="button" 
                @click="detectPrinters()" 
                :disabled="isDetecting"
                class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold rounded-xl transition shadow-xs cursor-pointer disabled:opacity-50"
            >
                <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" :class="isDetecting ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span x-text="isDetecting ? 'Detecting...' : 'Scan Printers'"></span>
            </button>
        </div>
    </div>

    <!-- Offline QZ Banner (Displays only if QZ Tray is not detected) -->
    <template x-if="!qzConnected">
        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl flex items-start space-x-3.5">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-xs text-amber-900 leading-relaxed space-y-1">
                <p class="font-bold">QZ Tray client not running on this computer.</p>
                <p>To enable silent direct printing to USB/Thermal printers without browser print dialogs, launch <strong class="font-semibold">QZ Tray</strong> on this workstation. Browser print dialog fallback will be used automatically in the meantime.</p>
                <div class="pt-1 flex items-center space-x-4">
                    <button type="button" @click="detectPrinters()" class="font-bold underline text-amber-800 hover:text-amber-950 cursor-pointer">
                        Check Connection Again &rarr;
                    </button>
                    <a href="https://qz.io/download/" target="_blank" rel="noopener" class="font-medium underline text-amber-700 hover:text-amber-900">
                        Download QZ Tray for Windows
                    </a>
                </div>
            </div>
        </div>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- LEFT COLUMN: Mapping Configuration Form (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                    <div>
                        <h2 class="text-base font-bold text-gray-900" x-text="isEditing ? 'Edit Printer Assignment' : 'Assign Printers for Workstation'"></h2>
                        <p class="text-xs text-gray-500">Route documents automatically based on hardware role.</p>
                    </div>
                    <template x-if="isEditing">
                        <button type="button" @click="resetForm()" class="text-xs font-semibold text-rose-600 hover:text-rose-800">
                            Cancel Edit
                        </button>
                    </template>
                </div>

                <form @submit.prevent="saveMapping()" class="space-y-4">
                    <!-- Branch & Workstation row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Branch
                            </label>
                            <select 
                                x-model="formData.branch_id"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849]"
                            >
                                <option value="">Global (All Branches / Default)</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->branch_code ?? 'Main' }})</option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-gray-400 mt-1 block">Leave global or assign to a specific location.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Printing Workstation <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="formData.workstation_name" 
                                    required
                                    placeholder="e.g. FrontDesk-PC or Cashier-01"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849]"
                                />
                                <button 
                                    type="button" 
                                    @click="formData.workstation_name = currentWorkstation"
                                    class="absolute right-2 top-2 px-2 py-0.5 text-[10px] font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition"
                                    title="Fill with this computer's name"
                                >
                                    Current
                                </button>
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">Identifier for the local PC sending print jobs.</span>
                        </div>
                    </div>

                    <!-- Printer 1: Bill Printer -->
                    <div class="p-4 bg-slate-50/75 rounded-xl border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#0B1849] uppercase tracking-wider flex items-center">
                                <span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span>
                                Printer 1: Bill Printer
                            </label>
                            <span class="text-[10px] font-semibold text-gray-500 bg-white px-2 py-0.5 rounded-md border border-gray-200">
                                Bills &bull; POS &bull; Invoices &bull; Payments
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                            <div class="sm:col-span-9">
                                <select 
                                    x-model="formData.bill_printer"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849]"
                                >
                                    <option value="">-- Select Installed Printer --</option>
                                    <template x-for="p in availablePrinters" :key="p">
                                        <option :value="p" x-text="p"></option>
                                    </template>
                                    <option value="__custom__" x-show="!availablePrinters.includes(formData.bill_printer) && formData.bill_printer">
                                        Custom / Saved: <span x-text="formData.bill_printer"></span>
                                    </option>
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <button 
                                    type="button" 
                                    @click="testPrinter('bill')" 
                                    :disabled="!formData.bill_printer || isTestingBill"
                                    class="w-full h-full inline-flex items-center justify-center px-3 py-2 bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-800 text-xs font-bold rounded-xl transition cursor-pointer disabled:opacity-50"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1" :class="isTestingBill ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    <span x-text="isTestingBill ? 'Printing...' : 'Test Bill'"></span>
                                </button>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-500">Prints customer bills, direct sale POS slips, 80mm sales invoices, advance receipts, and installment receipts.</p>
                    </div>

                    <!-- Printer 2: Job Card Printer -->
                    <div class="p-4 bg-slate-50/75 rounded-xl border border-slate-200/80 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-[#0B1849] uppercase tracking-wider flex items-center">
                                <span class="w-2 h-2 rounded-full bg-indigo-600 mr-2"></span>
                                Printer 2: Job Card Printer
                            </label>
                            <span class="text-[10px] font-semibold text-gray-500 bg-white px-2 py-0.5 rounded-md border border-gray-200">
                                Job Cards &bull; Selected Items &bull; Workshop
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                            <div class="sm:col-span-9">
                                <select 
                                    x-model="formData.job_card_printer"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849]"
                                >
                                    <option value="">-- Select Installed Printer --</option>
                                    <template x-for="p in availablePrinters" :key="p">
                                        <option :value="p" x-text="p"></option>
                                    </template>
                                    <option value="__custom__" x-show="!availablePrinters.includes(formData.job_card_printer) && formData.job_card_printer">
                                        Custom / Saved: <span x-text="formData.job_card_printer"></span>
                                    </option>
                                </select>
                            </div>
                            <div class="sm:col-span-3">
                                <button 
                                    type="button" 
                                    @click="testPrinter('job_card')" 
                                    :disabled="!formData.job_card_printer || isTestingJob"
                                    class="w-full h-full inline-flex items-center justify-center px-3 py-2 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-indigo-800 text-xs font-bold rounded-xl transition cursor-pointer disabled:opacity-50"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1" :class="isTestingJob ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    <span x-text="isTestingJob ? 'Printing...' : 'Test Job'"></span>
                                </button>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-500">Prints full vehicle job cards, selected services/products, and workshop technician instructions.</p>
                    </div>

                    <!-- Paper Size & Auto Route Toggle -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Thermal Paper Size
                            </label>
                            <select 
                                x-model="formData.paper_size"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849]"
                            >
                                <option value="80mm">80mm Thermal Paper (Standard)</option>
                                <option value="A4">A4 Full Sheet (Manual Feed)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Auto Route Printing
                            </label>
                            <div class="flex items-center space-x-3 mt-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="formData.auto_route" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0B1849]"></div>
                                    <span class="ml-2.5 text-xs font-semibold" :class="formData.auto_route ? 'text-[#0B1849]' : 'text-gray-500'" x-text="formData.auto_route ? 'Auto Route: ON' : 'Auto Route: OFF'"></span>
                                </label>
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">When ON, automatically dispatches to target printer silently.</span>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Notes / Station Description (Optional)
                        </label>
                        <input 
                            type="text" 
                            x-model="formData.notes" 
                            placeholder="e.g., Cashier counter double thermal setup"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849]"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3 flex items-center justify-end space-x-3">
                        <button 
                            type="submit" 
                            :disabled="isSaving"
                            class="inline-flex items-center px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer disabled:opacity-50"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="isSaving ? 'Saving...' : (isEditing ? 'Update Printer Mapping' : 'Save Printer Mapping')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT COLUMN: Detected Local Printers & Workstation Switcher (5 cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Workstation Local Configuration Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Local Workstation Settings</h3>
                    <span class="text-[10px] font-bold text-slate-500 uppercase bg-slate-100 px-2 py-0.5 rounded-md">Client PC</span>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-medium text-gray-600">
                        This Computer's Registered Workstation Name:
                    </label>
                    <div class="flex items-center space-x-2">
                        <input 
                            type="text" 
                            x-model="newWorkstationInput" 
                            placeholder="e.g. FrontDesk-PC"
                            class="flex-1 px-3 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-800 bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20"
                        />
                        <button 
                            type="button" 
                            @click="applyNewWorkstation()" 
                            class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition cursor-pointer"
                        >
                            Set
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400">Settings are remembered on this browser and linked to the hardware assignments on the left.</p>
                </div>
            </div>

            <!-- Detected Printers List Card -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-sm font-bold text-gray-900">Detected Windows Printers</h3>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700" x-text="availablePrinters.length"></span>
                    </div>
                    <button 
                        type="button" 
                        @click="detectPrinters()" 
                        class="text-xs font-semibold text-blue-600 hover:text-blue-800 cursor-pointer"
                    >
                        Rescan
                    </button>
                </div>

                <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1">
                    <template x-if="availablePrinters.length === 0">
                        <div class="text-center py-6 text-gray-400 text-xs italic">
                            <span x-show="qzConnected">No Windows printers reported by QZ Tray.</span>
                            <span x-show="!qzConnected">Start QZ Tray to auto-detect installed USB &amp; network printers.</span>
                        </div>
                    </template>

                    <template x-for="printer in availablePrinters" :key="printer">
                        <div class="p-3 bg-slate-50 hover:bg-slate-100/80 rounded-xl border border-slate-200/60 flex items-center justify-between text-xs transition">
                            <div class="truncate mr-2">
                                <div class="font-bold text-gray-800 truncate" x-text="printer"></div>
                                <div class="text-[10px] text-gray-400 flex items-center space-x-2">
                                    <span x-show="formData.bill_printer === printer" class="text-blue-600 font-semibold">[Bill]</span>
                                    <span x-show="formData.job_card_printer === printer" class="text-indigo-600 font-semibold">[Job Card]</span>
                                    <span>Thermal / System</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-1 shrink-0">
                                <button 
                                    type="button" 
                                    @click="formData.bill_printer = printer"
                                    title="Assign as Bill Printer"
                                    class="px-2 py-1 bg-white hover:bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-[10px] font-bold transition cursor-pointer"
                                >
                                    + Bill
                                </button>
                                <button 
                                    type="button" 
                                    @click="formData.job_card_printer = printer"
                                    title="Assign as Job Card Printer"
                                    class="px-2 py-1 bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg text-[10px] font-bold transition cursor-pointer"
                                >
                                    + Job
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

    </div>

    <!-- SAVED WORKSTATION PRINTER MAPPINGS TABLE -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-base font-bold text-gray-900">Saved Workstation Printer Mappings</h2>
                <p class="text-xs text-gray-500">Configured printer profiles active across all AutoCare centers.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                Total Configured: {{ count($settings) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-4">Branch</th>
                        <th class="py-3 px-4">Workstation</th>
                        <th class="py-3 px-4">Bill Printer</th>
                        <th class="py-3 px-4">Job Card Printer</th>
                        <th class="py-3 px-4">Paper</th>
                        <th class="py-3 px-4">Auto Route</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($settings as $setting)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3 px-4 font-semibold text-gray-900">
                                @if($setting->branch)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-blue-50 text-blue-700">
                                        {{ $setting->branch->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700">
                                        Global (All)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-[#0B1849]">
                                {{ $setting->workstation_name }}
                            </td>
                            <td class="py-3 px-4">
                                @if($setting->bill_printer)
                                    <span class="font-medium text-gray-800">{{ $setting->bill_printer }}</span>
                                @else
                                    <span class="text-rose-500 italic text-[11px]">Not assigned</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($setting->job_card_printer)
                                    <span class="font-medium text-gray-800">{{ $setting->job_card_printer }}</span>
                                @else
                                    <span class="text-rose-500 italic text-[11px]">Not assigned</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-600 font-mono text-[11px]">
                                {{ $setting->paper_size }}
                            </td>
                            <td class="py-3 px-4">
                                @if($setting->auto_route)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                        Prompt
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button 
                                    type="button" 
                                    @click="editMapping({{ json_encode($setting) }})"
                                    class="text-xs font-bold text-blue-600 hover:text-blue-800 transition cursor-pointer"
                                >
                                    Edit
                                </button>
                                <form action="{{ route('admin.printer-settings.destroy', $setting) }}" method="POST" class="inline" onsubmit="return confirm('Delete printer mapping for {{ $setting->workstation_name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 transition cursor-pointer">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-gray-400 italic">
                                No printer profiles configured yet. Add your workstation mapping above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RECENT PRINT ACTIVITY AUDIT LOG -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Recent Printing Audit Log</h3>
                <p class="text-[11px] text-gray-500">Tracks print submissions, QZ silent executions, and error statuses.</p>
            </div>
            <span class="text-[11px] font-medium text-gray-400">Live Recording</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                        <th class="py-2.5 px-4">Time</th>
                        <th class="py-2.5 px-4">Document</th>
                        <th class="py-2.5 px-4">Workstation</th>
                        <th class="py-2.5 px-4">Target Printer</th>
                        <th class="py-2.5 px-4">Status</th>
                        <th class="py-2.5 px-4">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50/40 text-[11px]">
                            <td class="py-2 px-4 text-gray-500 font-mono">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-2 px-4 font-bold text-gray-800 uppercase">{{ str_replace('_', ' ', $log->document_type) }}</td>
                            <td class="py-2 px-4 text-gray-600">{{ $log->workstation_name ?? '-' }}</td>
                            <td class="py-2 px-4 text-gray-700 truncate max-w-[150px]">{{ $log->printer_name ?? 'None' }}</td>
                            <td class="py-2 px-4">
                                @if($log->status === 'printed')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">Printed</span>
                                @elseif($log->status === 'submitted')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">Submitted</span>
                                @elseif($log->status === 'fallback_browser')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700">Browser Print</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700" title="{{ $log->error_message }}">Failed</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 text-gray-500">{{ $log->user?->name ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-400 italic">No print logs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
function printerSettingsComponent() {
    return {
        qzConnected: false,
        isDetecting: false,
        isSaving: false,
        isTestingBill: false,
        isTestingJob: false,
        isEditing: false,
        editingId: null,
        availablePrinters: [],
        currentWorkstation: 'Default-Workstation',
        newWorkstationInput: '',

        formData: {
            branch_id: '',
            workstation_name: '',
            bill_printer: '',
            job_card_printer: '',
            paper_size: '80mm',
            auto_route: true,
            notes: ''
        },

        initComponent() {
            this.currentWorkstation = window.AutoCarePrinter ? window.AutoCarePrinter.getWorkstation() : 'Default-Workstation';
            this.newWorkstationInput = this.currentWorkstation;
            this.formData.workstation_name = this.currentWorkstation;

            // Listen for QZ connection events
            window.addEventListener('autocare:printer-status', (e) => {
                this.qzConnected = e.detail.connected;
            });

            // Initial detection
            this.detectPrinters();
        },

        async detectPrinters() {
            this.isDetecting = true;
            try {
                if (window.AutoCarePrinter) {
                    await window.AutoCarePrinter.connect().catch(() => {});
                    this.qzConnected = window.AutoCarePrinter.isConnected;
                    const printers = await window.AutoCarePrinter.listPrinters();
                    this.availablePrinters = printers || [];
                }
            } catch (err) {
                console.warn('Printer detection error:', err);
            } finally {
                this.isDetecting = false;
            }
        },

        applyNewWorkstation() {
            const val = this.newWorkstationInput.trim();
            if (!val) return;
            this.currentWorkstation = val;
            this.formData.workstation_name = val;
            if (window.AutoCarePrinter) {
                window.AutoCarePrinter.setWorkstation(val);
                window.AutoCarePrinter.showToast('Workstation updated to: ' + val, 'success');
            }
        },

        editMapping(setting) {
            this.isEditing = true;
            this.editingId = setting.id;
            this.formData = {
                branch_id: setting.branch_id || '',
                workstation_name: setting.workstation_name || '',
                bill_printer: setting.bill_printer || '',
                job_card_printer: setting.job_card_printer || '',
                paper_size: setting.paper_size || '80mm',
                auto_route: !!setting.auto_route,
                notes: setting.notes || ''
            };
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetForm() {
            this.isEditing = false;
            this.editingId = null;
            this.formData = {
                branch_id: '',
                workstation_name: this.currentWorkstation,
                bill_printer: '',
                job_card_printer: '',
                paper_size: '80mm',
                auto_route: true,
                notes: ''
            };
        },

        async saveMapping() {
            this.isSaving = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = this.isEditing 
                ? `/admin/printer-settings/${this.editingId}`
                : `/admin/printer-settings`;
            const method = this.isEditing ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (window.AutoCarePrinter) {
                        window.AutoCarePrinter.showToast(data.message, 'success');
                    }
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    alert(data.message || 'Error saving printer settings.');
                }
            } catch (err) {
                alert('Connection error saving printer settings: ' + err.message);
            } finally {
                this.isSaving = false;
            }
        },

        async testPrinter(role) {
            const isBill = role === 'bill';
            const printerName = isBill ? this.formData.bill_printer : this.formData.job_card_printer;
            if (!printerName) {
                alert(`Please select a ${isBill ? 'Bill' : 'Job Card'} printer first.`);
                return;
            }

            if (isBill) this.isTestingBill = true;
            else this.isTestingJob = true;

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const res = await fetch('/admin/printer-settings/test-print', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({
                        printer_role: role,
                        printer_name: printerName,
                        workstation_name: this.formData.workstation_name || this.currentWorkstation,
                        branch_id: this.formData.branch_id || null
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.success) {
                    throw new Error(data.message || 'Failed to generate test print slip.');
                }

                // Send to QZ Tray
                if (window.AutoCarePrinter) {
                    await window.AutoCarePrinter.printDocument({
                        type: role,
                        html: data.html,
                        title: `Test Print - ${role.toUpperCase()}`,
                        documentId: null,
                        documentNumber: 'TEST-' + role.toUpperCase()
                    });
                }
            } catch (err) {
                alert('Test print error: ' + err.message);
            } finally {
                if (isBill) this.isTestingBill = false;
                else this.isTestingJob = false;
            }
        }
    };
}
</script>
@endpush
@endsection
