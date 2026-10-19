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
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    class="fixed top-16 bottom-0 left-0 z-20 w-64 bg-white border-r border-gray-200 transition-transform duration-200 ease-in-out flex flex-col justify-between overflow-y-auto"
>
    <div class="py-5 px-3 space-y-6">
        <!-- Dashboard Navigation -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                Core
            </div>
            <nav class="space-y-1">
                <a 
                    href="{{ route('dashboard') }}" 
                    class="group flex items-center px-3 py-2.5 text-sm rounded-lg transition {{ request()->routeIs('dashboard', 'home') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] font-medium hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard', 'home') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>
            </nav>
        </div>

        <!-- Master Data Collapsible Section -->
        <div x-data="{ masterDataOpen: true }">
            <button 
                type="button" 
                @click="masterDataOpen = !masterDataOpen"
                class="w-full flex items-center justify-between px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-gray-500 hover:text-gray-900 transition rounded-md group focus:outline-none"
            >
                <span class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7c-2 0-3 1-3 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-6-8h6" />
                    </svg>
                    <span class="text-gray-700">MASTER DATA</span>
                </span>
                <svg 
                    :class="masterDataOpen ? 'rotate-180 text-gray-600' : 'text-gray-400'" 
                    class="w-4 h-4 transform transition-transform duration-150" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Collapsible Sub-menu -->
            <nav x-show="masterDataOpen" x-transition.duration.150ms class="mt-2 space-y-1 pl-1">
                <!-- Service Lists -->
                <a 
                    href="{{ route('master-data.services.index') }}" 
                    class="group flex items-center px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('master-data.services.*') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] font-medium hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    <svg class="mr-3 h-4 w-4 flex-shrink-0 {{ request()->routeIs('master-data.services.*') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Service Lists
                </a>

                <!-- Vehicle Types -->
                <a 
                    href="{{ route('master-data.vehicle-types.index') }}" 
                    class="group flex items-center px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('master-data.vehicle-types.*') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] font-medium hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    <svg class="mr-3 h-4 w-4 flex-shrink-0 {{ request()->routeIs('master-data.vehicle-types.*') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Vehicle Types
                </a>

                <!-- Manage Service Payment -->
                <a 
                    href="{{ route('master-data.service-payments.index') }}" 
                    class="group flex items-center px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('master-data.service-payments.*') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] font-medium hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    <svg class="mr-3 h-4 w-4 flex-shrink-0 {{ request()->routeIs('master-data.service-payments.*') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Manage Service Payment
                </a>

                <!-- Company Details -->
                <a 
                    href="{{ route('master-data.company-details.edit') }}" 
                    class="group flex items-center px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('master-data.company-details.*') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] font-medium hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    <svg class="mr-3 h-4 w-4 flex-shrink-0 {{ request()->routeIs('master-data.company-details.*') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Company Details
                </a>

                <!-- Search Client SMS -->
                <a 
                    href="{{ route('master-data.client-sms.index') }}" 
                    class="group flex items-center px-3 py-2 text-sm rounded-lg transition {{ request()->routeIs('master-data.client-sms.*') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] font-medium hover:bg-gray-100 hover:text-gray-900' }}"
                >
                    <svg class="mr-3 h-4 w-4 flex-shrink-0 {{ request()->routeIs('master-data.client-sms.*') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    Search Client SMS
                </a>
            </nav>
        </div>

        @if(Auth::user() && Auth::user()->isAdmin())
            <!-- Administration (Admin Only) -->
            <div>
                <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    Administration
                </div>
                <nav class="space-y-1">
                    <a 
                        href="{{ route('admin.staff.index') }}" 
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.staff.*') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] hover:bg-gray-100 hover:text-gray-900' }}"
                    >
                        <svg class="mr-3 h-4 w-4 flex-shrink-0 {{ request()->routeIs('admin.staff.*') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Staff Accounts
                    </a>
                    <a 
                        href="{{ route('admin.activity-logs.index') }}" 
                        class="group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('admin.activity-logs.*') ? 'bg-slate-100 text-[#0B1849] font-bold border-l-4 border-[#0B1849]' : 'text-[#4A4A4A] hover:bg-gray-100 hover:text-gray-900' }}"
                    >
                        <svg class="mr-3 h-4 w-4 flex-shrink-0 {{ request()->routeIs('admin.activity-logs.*') ? 'text-[#0B1849]' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Activity Logs
                    </a>
                </nav>
            </div>
        @endif

        <!-- Future Modules (Clearly marked as Soon) -->
        <div>
            <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-gray-400 flex items-center justify-between">
                <span>Future Modules</span>
                <span class="text-[10px] text-gray-400 font-normal">Phase 2</span>
            </div>
            <div class="space-y-1">
                @php
                    $futureModules = [
                        ['name' => 'Jobs', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['name' => 'Customers & Vehicles', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                        ['name' => 'Company Fleet', 'icon' => 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'],
                        ['name' => 'Sales & Billing', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                        ['name' => 'Inventory', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                        ['name' => 'Employees & Payroll', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                        ['name' => 'Finance', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['name' => 'Reports', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ];
                @endphp

                @foreach($futureModules as $module)
                    <div class="flex items-center justify-between px-3 py-1.5 text-xs text-gray-400 rounded-lg cursor-not-allowed select-none bg-gray-50/50">
                        <div class="flex items-center">
                            <svg class="mr-3 h-4 w-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $module['icon'] }}" />
                            </svg>
                            <span>{{ $module['name'] }}</span>
                        </div>
                        <span class="text-[9px] uppercase font-semibold tracking-wider text-gray-400 bg-gray-200/60 px-1.5 py-0.5 rounded">Soon</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Center Status / Footer Info -->
    <div class="p-3 border-t border-gray-100 bg-gray-50/60">
        <div class="text-[11px] text-gray-500 font-medium flex items-center justify-between">
            <span>System Status</span>
            <span class="inline-flex items-center text-emerald-600 font-semibold text-[10px]">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                Online
            </span>
        </div>
        <div class="text-[10px] text-gray-400 mt-1">
            AutoCare Monolith v1.0
        </div>
    </div>
</aside>
