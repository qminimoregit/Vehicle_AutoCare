<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#0B1849]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sign In') | {{ config('app.name', 'AutoCare System') }}</title>
    <meta name="description" content="Secure portal sign in for AutoCare System.">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/branding/autocare-favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('images/branding/autocare-favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-900 text-gray-800 antialiased flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-linear-to-b from-[#0B1849] via-[#0e1d58] to-[#081236]">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-[1.02] focus:outline-hidden" title="AutoCare System">
            <div class="inline-flex items-center justify-center bg-white px-6 py-4 rounded-2xl shadow-xl shadow-black/25 border border-white/20 w-full max-w-[280px] sm:max-w-[320px] mx-auto">
                <img 
                    src="{{ asset('images/branding/autocare-logo-transparent.png') }}" 
                    alt="AutoCare System Logo" 
                    class="w-full h-auto object-contain max-h-20"
                >
            </div>
        </a>
        <p class="mt-3 text-xs tracking-widest uppercase text-slate-300 font-semibold">Center Management Portal</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 shadow-2xl rounded-2xl sm:px-10 border border-slate-100">
            @yield('content')
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} AutoCare System. All rights reserved.
        </div>
    </div>
</body>
</html>
