<header class="fixed top-0 left-0 right-0 z-30 h-16 bg-[#0B1849] border-b border-[#16276a] text-white flex items-center justify-between px-4 sm:px-6 shadow-sm">
    @php
        $isVehicleActive = request()->routeIs('operations.vehicles.*');
        $isDirectSaleActive = request()->routeIs('operations.direct-sales.*');
        $isJobCardActive = request()->routeIs('operations.job-cards.*');

        $accessibleBranches = Auth::user() ? \App\Services\BranchContext::getAccessibleBranches() : collect();
        $activeBranchId = \App\Services\BranchContext::getActiveBranchId();
        $currentBranch = $accessibleBranches->firstWhere('id', $activeBranchId) ?? $accessibleBranches->first();
        $showBranchSelector = $accessibleBranches->count() > 1;
    @endphp

    <!-- Left Section: Brand & Mobile Menu Toggle -->
    <div class="flex items-center space-x-3 shrink-0">
        <!-- Mobile Sidebar Button -->
        <button 
            type="button" 
            @click="sidebarOpen = !sidebarOpen" 
            class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-slate-300 hover:text-white hover:bg-[#15286e] focus:outline-none focus:ring-2 focus:ring-white transition"
            aria-label="Toggle Navigation Sidebar"
        >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Brand Logo & Name -->
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group" title="Viran Auto Care Dashboard">
            <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center shadow-xs shrink-0 ring-1 ring-white/20 group-hover:scale-105 transition-transform duration-150">
                <img 
                    src="{{ asset('images/branding/viran_auto_care_logo.png') }}" 
                    alt="Viran Auto Care Logo" 
                    class="w-8 h-8 object-contain"
                    onerror="this.onerror=null; this.src='{{ asset('storage/company/viran_auto_care_logo.png') }}';"
                >
            </div>
            <div>
                <span class="text-base sm:text-lg font-bold tracking-tight text-white block leading-none font-sans uppercase">VIRAN AUTO CARE</span>
                <span class="text-[9px] sm:text-[10px] tracking-widest text-slate-300 font-medium uppercase mt-0.5 block">CENTER MANAGEMENT</span>
            </div>
        </a>
    </div>

    <!-- Center Section: Quick Shortcut Buttons (Desktop & Tablet) -->
    @auth
        <nav id="topbar-shortcuts" class="hidden md:flex items-center space-x-1.5 lg:space-x-2.5 mx-2" aria-label="Operations Quick Shortcuts">
            <!-- 1. Vehicle Registration Shortcut -->
            <a 
                href="{{ route('operations.vehicles.index') }}" 
                class="inline-flex items-center px-2.5 lg:px-3 py-1.5 rounded-lg text-xs font-semibold transition duration-150 border {{ $isVehicleActive ? 'bg-[#1e3a8a] text-white border-blue-400/40 shadow-xs ring-1 ring-blue-400/30' : 'bg-[#15286e]/80 hover:bg-[#1f3796] text-white/90 hover:text-white border-white/10' }}"
                title="Vehicle Registration"
            >
                <svg class="w-3.5 h-3.5 mr-1.5 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM5 10l1.5-4.5A2 2 0 018.4 4h7.2a2 2 0 011.9 1.5L19 10m-14 0h14m-14 0v6a1 1 0 001 1h1m12-7v6a1 1 0 01-1 1h-1" />
                </svg>
                <span class="hidden lg:inline">Vehicle Registration</span>
                <span class="inline lg:hidden">Vehicle Reg</span>
            </a>

            <!-- 2. Direct Sale (POS) Shortcut -->
            <a 
                href="{{ route('operations.direct-sales.index') }}" 
                class="inline-flex items-center px-2.5 lg:px-3 py-1.5 rounded-lg text-xs font-semibold transition duration-150 border {{ $isDirectSaleActive ? 'bg-[#1e3a8a] text-white border-blue-400/40 shadow-xs ring-1 ring-blue-400/30' : 'bg-[#15286e]/80 hover:bg-[#1f3796] text-white/90 hover:text-white border-white/10' }}"
                title="Direct Sale (POS)"
            >
                <svg class="w-3.5 h-3.5 mr-1.5 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Direct Sale</span>
            </a>

            <!-- 3. Job Cards Shortcut -->
            <a 
                href="{{ route('operations.job-cards.index') }}" 
                class="inline-flex items-center px-2.5 lg:px-3 py-1.5 rounded-lg text-xs font-semibold transition duration-150 border {{ $isJobCardActive ? 'bg-[#1e3a8a] text-white border-blue-400/40 shadow-xs ring-1 ring-blue-400/30' : 'bg-[#15286e]/80 hover:bg-[#1f3796] text-white/90 hover:text-white border-white/10' }}"
                title="Job Card Management"
            >
                <svg class="w-3.5 h-3.5 mr-1.5 shrink-0 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span>Job Cards</span>
            </a>
        </nav>
    @endauth

    <!-- Right Section: User & Role & Dropdown -->
    <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
        @auth
            <!-- Branch Selector Dropdown (Shown only when user has access to > 1 branch) -->
            @if($showBranchSelector)
                <div class="relative" x-data="{ branchDropdownOpen: false }" @click.outside="branchDropdownOpen = false">
                    <button 
                        type="button" 
                        @click="branchDropdownOpen = !branchDropdownOpen"
                        class="inline-flex items-center space-x-1.5 px-2.5 sm:px-3 py-1.5 rounded-lg bg-[#15286e]/80 hover:bg-[#1f3796] text-white border border-white/10 text-xs font-semibold transition focus:outline-none focus:ring-1 focus:ring-white/40 cursor-pointer"
                        aria-label="Select Active Branch"
                        aria-expanded="false"
                        title="Active Branch"
                    >
                        <svg class="w-3.5 h-3.5 text-blue-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="truncate max-w-[110px] sm:max-w-[150px]">{{ $currentBranch ? $currentBranch->branch_name : 'Select Branch' }}</span>
                        <svg class="w-3 h-3 text-slate-300 transform transition-transform duration-150 shrink-0" :class="branchDropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Branch Dropdown Menu -->
                    <div 
                        x-show="branchDropdownOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-64 rounded-xl bg-white py-1.5 shadow-xl ring-1 ring-black/10 text-gray-800 z-50 divide-y divide-gray-100"
                        style="display: none;"
                    >
                        <div class="px-3.5 py-2">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Switch Active Branch</div>
                            <div class="text-xs text-gray-500 mt-0.5">Records and operations will filter by selected branch.</div>
                        </div>

                        <div class="py-1 max-h-60 overflow-y-auto">
                            @foreach($accessibleBranches as $branchOption)
                                @php $isSelected = ($currentBranch && $currentBranch->id === $branchOption->id); @endphp
                                <form method="POST" action="{{ route('branches.switch') }}">
                                    @csrf
                                    <input type="hidden" name="branch_id" value="{{ $branchOption->id }}">
                                    <button 
                                        type="submit" 
                                        class="w-full text-left flex items-center justify-between px-3.5 py-2 text-xs transition {{ $isSelected ? 'bg-blue-50 text-[#0B1849] font-bold' : 'text-gray-700 hover:bg-slate-50' }} cursor-pointer"
                                    >
                                        <div class="flex items-center space-x-2 min-w-0">
                                            <span class="w-2 h-2 rounded-full shrink-0 {{ $isSelected ? 'bg-blue-600' : 'bg-gray-300' }}"></span>
                                            <div class="truncate">
                                                <div class="truncate leading-tight">{{ $branchOption->branch_name }}</div>
                                                <div class="text-[10px] text-gray-400 font-mono leading-tight mt-0.5">{{ $branchOption->branch_code }}</div>
                                            </div>
                                        </div>
                                        @if($branchOption->is_main_branch)
                                            <span class="ml-2 text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-bold shrink-0">Main</span>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Mobile Quick Actions Dropdown (< md) -->
            <div class="md:hidden relative" x-data="{ quickMenuOpen: false }" @click.outside="quickMenuOpen = false">
                <button 
                    type="button" 
                    @click="quickMenuOpen = !quickMenuOpen"
                    class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-lg bg-[#15286e]/80 hover:bg-[#1f3796] text-white border border-white/10 text-xs font-semibold transition focus:outline-none focus:ring-1 focus:ring-white/40"
                    aria-label="Quick Actions Shortcuts"
                    aria-expanded="false"
                >
                    <svg class="w-3.5 h-3.5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="text-[11px] font-bold">Shortcuts</span>
                    <svg class="w-3 h-3 text-slate-300 transform transition-transform duration-150" :class="quickMenuOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Mobile Shortcuts Dropdown Menu -->
                <div 
                    x-show="quickMenuOpen"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 rounded-xl bg-white py-1.5 shadow-xl ring-1 ring-black/10 text-gray-800 z-50 divide-y divide-gray-100"
                    style="display: none;"
                >
                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400">
                        Quick Shortcuts
                    </div>

                    <div class="py-1 space-y-0.5">
                        <!-- Vehicle Registration -->
                        <a 
                            href="{{ route('operations.vehicles.index') }}" 
                            class="flex items-center px-3 py-2 text-xs font-semibold rounded-md mx-1 transition {{ $isVehicleActive ? 'bg-[#EEF1FA] text-[#0B1849] font-bold' : 'text-gray-700 hover:bg-slate-100' }}"
                        >
                            <div class="w-6 h-6 rounded-md bg-[#0B1849] text-white flex items-center justify-center mr-2.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM5 10l1.5-4.5A2 2 0 018.4 4h7.2a2 2 0 011.9 1.5L19 10m-14 0h14m-14 0v6a1 1 0 001 1h1m12-7v6a1 1 0 01-1 1h-1" />
                                </svg>
                            </div>
                            <span>Vehicle Registration</span>
                        </a>

                        <!-- Direct Sale -->
                        <a 
                            href="{{ route('operations.direct-sales.index') }}" 
                            class="flex items-center px-3 py-2 text-xs font-semibold rounded-md mx-1 transition {{ $isDirectSaleActive ? 'bg-[#EEF1FA] text-[#0B1849] font-bold' : 'text-gray-700 hover:bg-slate-100' }}"
                        >
                            <div class="w-6 h-6 rounded-md bg-[#0B1849] text-white flex items-center justify-center mr-2.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span>Direct Sale</span>
                        </a>

                        <!-- Job Cards -->
                        <a 
                            href="{{ route('operations.job-cards.index') }}" 
                            class="flex items-center px-3 py-2 text-xs font-semibold rounded-md mx-1 transition {{ $isJobCardActive ? 'bg-[#EEF1FA] text-[#0B1849] font-bold' : 'text-gray-700 hover:bg-slate-100' }}"
                        >
                            <div class="w-6 h-6 rounded-md bg-[#0B1849] text-white flex items-center justify-center mr-2.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <span>Job Cards</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Menu Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                <button 
                    @click="open = !open" 
                    type="button" 
                    class="flex items-center space-x-3 p-1.5 rounded-lg hover:bg-[#15286e] text-left transition focus:outline-none focus:ring-2 focus:ring-blue-400"
                    id="user-menu-button"
                    aria-expanded="false"
                    aria-haspopup="true"
                >
                    <!-- User Avatar -->
                    <div class="w-8 h-8 rounded-full bg-slate-700 border border-slate-500/40 text-white font-semibold text-xs flex items-center justify-center uppercase shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>

                    <!-- User Name & Role (Hidden on small mobile) -->
                    <div class="hidden sm:block text-left">
                        <div class="text-xs font-semibold text-white leading-tight flex items-center space-x-1.5">
                            <span>{{ Auth::user()->name }}</span>
                            @if(Auth::user()->isAdmin())
                                <span class="bg-amber-400/20 text-amber-300 border border-amber-400/30 text-[10px] px-1.5 py-0.2 rounded font-semibold uppercase tracking-wider">Admin</span>
                            @else
                                <span class="bg-sky-400/20 text-sky-300 border border-sky-400/30 text-[10px] px-1.5 py-0.2 rounded font-semibold uppercase tracking-wider">Staff</span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-300 font-normal leading-tight mt-0.5 truncate max-w-[140px]">
                            {{ Auth::user()->email }}
                        </div>
                    </div>

                    <!-- Chevron Icon -->
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Card -->
                <div 
                    x-show="open" 
                    x-transition:enter="transition ease-out duration-100" 
                    x-transition:enter-start="transform opacity-0 scale-95" 
                    x-transition:enter-end="transform opacity-100 scale-100" 
                    x-transition:leave="transition ease-in duration-75" 
                    x-transition:leave-start="transform opacity-100 scale-100" 
                    x-transition:leave-end="transform opacity-0 scale-95" 
                    class="absolute right-0 mt-2 w-56 rounded-lg bg-white py-2 shadow-xl ring-1 ring-black/10 text-gray-800 z-50 divide-y divide-gray-100"
                    style="display: none;"
                >
                    <div class="px-4 py-2">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Signed in as</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        <div class="mt-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium {{ Auth::user()->isAdmin() ? 'bg-indigo-50 text-indigo-700' : 'bg-sky-50 text-sky-700' }}">
                                Role: {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                    </div>

                    @if(Auth::user()->isAdmin())
                        <div class="py-1">
                            <a href="{{ route('admin.staff.index') }}" class="flex items-center px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Manage Staff Accounts
                            </a>
                            <a href="{{ route('admin.activity-logs.index') }}" class="flex items-center px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                System Activity Logs
                            </a>
                        </div>
                    @endif

                    <div class="py-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition text-left">
                                <svg class="w-4 h-4 mr-2 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Secure Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="text-xs font-semibold bg-blue-600 hover:bg-blue-500 px-3 py-1.5 rounded text-white transition">Sign In</a>
        @endauth
    </div>
</header>
