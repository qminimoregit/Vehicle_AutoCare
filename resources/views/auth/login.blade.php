@extends('layouts.auth')

@section('title', 'Sign In')

@push('preload')
    <link rel="preload" as="image" href="{{ asset('images/login/vehicle-01.webp') }}" type="image/webp">
    <link rel="preload" as="image" href="{{ asset('images/login/vehicle-02.webp') }}" type="image/webp">
    <link rel="preload" as="image" href="{{ asset('images/branding/viran_auto_care_logo.png') }}">
@endpush

@php
    $slides = [
        [
            'image' => 'images/login/vehicle-01.webp',
            'fallback' => 'images/login/vehicle-01.jpg',
            'alt' => 'Luxury Range Rover SUV in modern detailing bay',
            'tag' => 'Luxury SUV Care',
            'title' => 'Professional Vehicle Services',
            'desc' => 'Deliver superior auto detailing, inspections, and customer service with precision.'
        ],
        [
            'image' => 'images/login/vehicle-02.webp',
            'fallback' => 'images/login/vehicle-02.jpg',
            'alt' => 'Modern luxury sedan undergoing computerized diagnostics in service bay',
            'tag' => 'Modern Sedan Diagnostics',
            'title' => 'Manage Your Auto Care Center',
            'desc' => 'High-accuracy fault diagnostics, electronic calibrations, and comprehensive service records.'
        ],
        [
            'image' => 'images/login/vehicle-03.webp',
            'fallback' => 'images/login/vehicle-03.jpg',
            'alt' => 'Modern performance sports car on high-gloss workshop floor',
            'tag' => 'Sports Car & Performance',
            'title' => 'Precision Mechanical Service',
            'desc' => 'Complete operational control, scheduling, and high-performance workshop tracking.'
        ],
        [
            'image' => 'images/login/vehicle-04.webp',
            'fallback' => 'images/login/vehicle-04.jpg',
            'alt' => 'Rugged premium Jeep Wrangler 4x4 in off-road service bay',
            'tag' => 'Premium Jeep Maintenance',
            'title' => 'Manage Every Job with Confidence',
            'desc' => 'Track technician assignments, issue spare parts, and verify repair stages in real time.'
        ],
        [
            'image' => 'images/login/vehicle-05.webp',
            'fallback' => 'images/login/vehicle-05.jpg',
            'alt' => '4x4 double cab pickup truck on hydraulic workshop service lift',
            'tag' => 'Pickup & Fleet Care',
            'title' => 'Smart Fleet & Van Care',
            'desc' => 'Full chassis, suspension, tire care, and preventive fleet maintenance with live updates.'
        ],
        [
            'image' => 'images/login/vehicle-06.webp',
            'fallback' => 'images/login/vehicle-06.jpg',
            'alt' => 'Modern luxury 4x4 Land Rover Defender in state-of-the-art service center',
            'tag' => 'Luxury 4x4 Vehicle Care',
            'title' => 'Customers, Vehicles & Invoices',
            'desc' => 'Instant invoice creation, multi-method payment collection, and complete customer history.'
        ],
    ];
@endphp

@section('content')
<div class="min-h-screen lg:h-screen lg:overflow-hidden w-full flex flex-col lg:flex-row bg-white">
    
    <!-- ========================================== -->
    <!-- LEFT SIDE: Login Form (~45% on desktop)    -->
    <!-- ========================================== -->
    <div class="w-full lg:w-[45%] xl:w-[42%] 2xl:w-[40%] flex flex-col justify-between bg-white px-6 py-8 sm:px-12 sm:py-10 lg:px-12 lg:py-10 xl:px-16 xl:py-12 z-20 overflow-y-auto thin-scrollbar">
        
        <!-- Top Section: Brand Logo -->
        <div>
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-3.5 group focus:outline-hidden" title="Viran Auto Care">
                <div class="w-13 h-13 rounded-2xl bg-white border border-slate-200/90 shadow-xs p-1.5 flex items-center justify-center shrink-0 group-hover:shadow-md transition">
                    <img 
                        src="{{ asset('images/branding/viran_auto_care_logo.png') }}" 
                        alt="Viran Auto Care Logo" 
                        class="w-full h-full object-contain"
                        onerror="this.onerror=null; this.src='{{ asset('storage/company/viran_auto_care_logo.png') }}';"
                    >
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0B1849] block leading-none">VIRAN AUTO CARE</span>
                    <span class="text-[10px] sm:text-[11px] tracking-widest uppercase text-slate-400 font-semibold mt-1 block">Center Management</span>
                </div>
            </a>
        </div>

        <!-- Center Section: Welcome & Login Form -->
        <div class="my-auto py-8 w-full max-w-md mx-auto lg:mx-0">
            
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#0B1849]">
                    Welcome Back
                </h1>
                <p class="text-sm text-gray-500 mt-2">
                    Sign in to manage your auto care center.
                </p>
            </div>

            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="mb-5 p-3.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl flex items-center shadow-xs">
                    <svg class="w-4 h-4 text-emerald-600 mr-2.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            <!-- General Error Alert -->
            @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 text-xs rounded-xl shadow-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form (Laravel Backend Auth Integration) -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5" novalidate>
                @csrf

                <!-- Email Address Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            autofocus
                            value="{{ old('email') }}" 
                            placeholder="e.g. admin@autocare.com"
                            class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849] @error('email') border-rose-400 bg-rose-50/30 focus:ring-rose-200 @enderror transition duration-150"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field with Show/Hide Toggle -->
                <div x-data="{ showPassword: false }">
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-700">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#0B1849] hover:text-blue-700 hover:underline transition">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative rounded-xl">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            autocomplete="current-password" 
                            required 
                            placeholder="Enter your password"
                            class="block w-full pl-10 pr-11 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 placeholder-gray-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-[#0B1849]/20 focus:border-[#0B1849] @error('password') border-rose-400 bg-rose-50/30 focus:ring-rose-200 @enderror transition duration-150"
                        />
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 focus:outline-hidden cursor-pointer"
                            :title="showPassword ? 'Hide password' : 'Show password'"
                            aria-label="Toggle password visibility"
                        >
                            <svg x-show="!showPassword" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center">
                    <input 
                        id="remember" 
                        name="remember" 
                        type="checkbox" 
                        {{ old('remember') ? 'checked' : '' }}
                        class="h-4 w-4 text-[#0B1849] focus:ring-[#0B1849] border-gray-300 rounded cursor-pointer accent-[#0B1849]"
                    />
                    <label for="remember" class="ml-2 block text-xs font-medium text-gray-600 cursor-pointer select-none">
                        Remember me on this browser
                    </label>
                </div>

                <!-- Sign In Button (Navy #0B1849) -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center py-3.5 px-5 rounded-xl font-semibold text-sm text-white bg-[#0B1849] hover:bg-[#13256b] active:bg-[#071033] shadow-md shadow-[#0B1849]/15 hover:shadow-lg hover:shadow-[#0B1849]/25 focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849] transition duration-200 cursor-pointer"
                    >
                        <span>Sign In to Viran Auto Care</span>
                        <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Bottom Footer -->
        <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400 gap-2">
            <span>&copy; {{ date('Y') }} Viran Auto Care. All rights reserved.</span>
            <span class="inline-flex items-center text-gray-400 font-medium">
                <svg class="w-3.5 h-3.5 mr-1 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Secure Portal
            </span>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- RIGHT SIDE: Automatic Vehicle Slider (55%) -->
    <!-- ========================================== -->
    <div 
        x-data="vehicleSlider()"
        x-init="initSlider()"
        @mouseenter="stopAutoplay()"
        @mouseleave="startAutoplay()"
        class="hidden lg:flex lg:w-[55%] xl:w-[58%] 2xl:w-[60%] relative h-full bg-[#0B1849] overflow-hidden select-none"
        @keydown.right.window="next()"
        @keydown.left.window="prev()"
    >
        <!-- Background Slide Images with Object-fit Cover and Smooth Fade Transitions -->
        @foreach($slides as $index => $slide)
            <div 
                class="absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none' }}"
                :class="active === {{ $index }} ? 'opacity-100 z-10 pointer-events-auto' : 'opacity-0 z-0 pointer-events-none'"
            >
                <picture class="w-full h-full">
                    <source srcset="{{ asset($slide['image']) }}" type="image/webp">
                    <img 
                        src="{{ asset($slide['fallback']) }}" 
                        alt="{{ $slide['alt'] }}" 
                        class="w-full h-full object-cover object-center"
                        loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                    />
                </picture>
                <!-- Subtle dark gradient overlay near bottom for readable white text -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/40 to-black/15"></div>
            </div>
        @endforeach

        <!-- Slide Overlay Content: Text & Controls -->
        <div class="relative z-20 w-full h-full flex flex-col justify-end p-8 sm:p-12 lg:p-14 xl:p-16 pointer-events-none">
            
            <!-- Marketing Headlines (Synchronized with each Slide) -->
            <div class="min-h-[140px] flex flex-col justify-end">
                @foreach($slides as $index => $slide)
                    <div 
                        x-show="active === {{ $index }}"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 translate-y-3"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-300 absolute"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="space-y-2.5 max-w-xl"
                        @if($index !== 0) style="display: none;" @endif
                    >
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wider uppercase bg-white/15 text-white/95 backdrop-blur-md border border-white/20">
                            {{ $slide['tag'] }}
                        </span>
                        <h2 class="text-2xl sm:text-3xl xl:text-4xl font-bold text-white tracking-tight leading-tight drop-shadow-md">
                            {{ $slide['title'] }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-normal drop-shadow-xs">
                            {{ $slide['desc'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Slider Navigation Row: Small Indicator Dots & Manual Arrow Navigation -->
            <div class="mt-8 flex items-center justify-between pt-5 border-t border-white/15 pointer-events-auto">
                
                <!-- Indicator Dots (● ○ ○ ○ ○ ○) -->
                <div class="flex items-center space-x-2.5" role="tablist" aria-label="Slider navigation">
                    @foreach($slides as $index => $slide)
                        <button 
                            type="button"
                            @click="goTo({{ $index }})"
                            class="transition-all duration-300 rounded-full focus:outline-hidden focus:ring-2 focus:ring-white/60 cursor-pointer"
                            :class="active === {{ $index }} ? 'w-8 h-2.5 bg-white shadow-xs' : 'w-2.5 h-2.5 bg-white/40 hover:bg-white/70'"
                            aria-label="Go to slide {{ $index + 1 }}: {{ $slide['title'] }}"
                        ></button>
                    @endforeach
                </div>

                <!-- Manual Prev / Next Arrows -->
                <div class="flex items-center space-x-2">
                    <button 
                        type="button" 
                        @click="prev()" 
                        class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 backdrop-blur-md text-white flex items-center justify-center border border-white/20 transition-all active:scale-95 focus:outline-hidden focus:ring-2 focus:ring-white/60 cursor-pointer"
                        title="Previous Slide"
                        aria-label="Previous Slide"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button 
                        type="button" 
                        @click="next()" 
                        class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 active:bg-white/30 backdrop-blur-md text-white flex items-center justify-center border border-white/20 transition-all active:scale-95 focus:outline-hidden focus:ring-2 focus:ring-white/60 cursor-pointer"
                        title="Next Slide"
                        aria-label="Next Slide"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function vehicleSlider() {
        return {
            active: 0,
            total: 6,
            timer: null,
            duration: 5000,
            prefersReducedMotion: false,

            initSlider() {
                // Check user preference for reduced motion
                this.prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                if (!this.prefersReducedMotion) {
                    this.startAutoplay();
                }

                // Pause image motion when tab/page is not visible
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) {
                        this.stopAutoplay();
                    } else if (!this.prefersReducedMotion) {
                        this.startAutoplay();
                    }
                });
            },

            startAutoplay() {
                this.stopAutoplay();
                this.timer = setInterval(() => {
                    this.next();
                }, this.duration);
            },

            stopAutoplay() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                }
            },

            next() {
                this.active = (this.active + 1) % this.total;
                if (!this.prefersReducedMotion) {
                    this.startAutoplay();
                }
            },

            prev() {
                this.active = (this.active - 1 + this.total) % this.total;
                if (!this.prefersReducedMotion) {
                    this.startAutoplay();
                }
            },

            goTo(index) {
                this.active = index;
                if (!this.prefersReducedMotion) {
                    this.startAutoplay();
                }
            }
        };
    }

    // Expose globally on window so Alpine can bind under all script-loading contexts
    window.vehicleSlider = vehicleSlider;
</script>
@endpush
