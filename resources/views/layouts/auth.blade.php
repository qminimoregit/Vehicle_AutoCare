<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
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

    <!-- Preload Assets -->
    @stack('preload')

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-gray-800 antialiased font-sans selection:bg-[#0B1849] selection:text-white">
    @yield('content')

    @stack('scripts')
</body>
</html>
