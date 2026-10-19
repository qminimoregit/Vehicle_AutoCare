<header class="fixed top-0 left-0 right-0 z-30 h-16 bg-[#0B1849] border-b border-[#16276a] text-white flex items-center justify-between px-4 sm:px-6 shadow-sm">
    <!-- Left Section: Brand & Mobile Menu Toggle -->
    <div class="flex items-center space-x-3">
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
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-inner font-bold text-lg tracking-wider group-hover:bg-blue-500 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <span class="text-lg font-bold tracking-tight text-white block leading-none">AutoCare System</span>
                <span class="text-[10px] tracking-widest text-slate-400 font-medium uppercase mt-0.5 block">Center Management</span>
            </div>
        </a>
    </div>

    <!-- Right Section: User & Role & Dropdown -->
    <div class="flex items-center space-x-4">
        @auth
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
