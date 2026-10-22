<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Viran Auto Care') }}</title>
    <meta name="description" content="Professional Auto Care Center Management System - Streamlined operations, service records, and fleet management.">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/branding/autocare-favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('images/branding/autocare-favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Vite Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F6F7F9] text-[#4A4A4A] antialiased" 
      x-data="{ 
          sidebarOpen: false, 
          sidebarCollapsed: localStorage.getItem('autocare_sidebar_collapsed') === 'true',
          toggleSidebarCollapse() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('autocare_sidebar_collapsed', this.sidebarCollapsed);
          }
      }" 
      @close-mobile-sidebar.window="sidebarOpen = false"
>
    <!-- Subtle Top Loading Indicator for Smooth Navigation -->
    <div id="page-loading-bar" class="fixed top-0 left-0 right-0 z-50 h-[3px] bg-gradient-to-r from-blue-500 via-indigo-500 to-[#0B1849] opacity-0 pointer-events-none transition-all duration-300 ease-out" style="width: 0%;"></div>

    <div class="min-h-full flex flex-col">
        <!-- Fixed Top Navigation -->
        <x-topbar />

        <div class="flex flex-1 pt-16">
            <!-- Left Sidebar Navigation -->
            <x-sidebar />

            <!-- Main Content Area: Full width on mobile/tablet, responsive left padding on desktop with collapse support -->
            <main 
                id="main-content" 
                :class="sidebarCollapsed ? 'lg:pl-0' : 'lg:pl-[270px]'"
                class="flex-1 w-full min-w-0 transition-[padding] duration-200 ease-in-out"
            >
                <div class="max-w-7xl 2xl:max-w-[1700px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
                    <!-- Global Notification / Flash Alerts -->
                    <x-alert />

                    <!-- View Content -->
                    {{ $slot ?? '' }}
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- QZ Tray & AutoCare Universal Printer Subsystem -->
    <script src="{{ asset('vendor/qz/qz-tray.js') }}"></script>
    <script src="{{ asset('js/autocare-printer.js') }}"></script>

    @stack('scripts')
</body>
</html>
