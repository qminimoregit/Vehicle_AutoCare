@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-900 tracking-tight">Password Recovery</h3>
        <p class="text-xs text-gray-500 mt-1">Enter your registered email address and we will provide reset instructions.</p>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 text-xs rounded-lg flex items-start space-x-2">
            <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p>{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-700">
                Email Address <span class="text-rose-500">*</span>
            </label>
            <div class="mt-1 relative rounded-lg shadow-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                    </svg>
                </div>
                <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    autocomplete="email" 
                    required 
                    value="{{ old('email') }}" 
                    placeholder="e.g. admin@autocare.com"
                    class="block w-full pl-9 pr-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('email') border-rose-400 bg-rose-50/30 @enderror transition"
                />
            </div>
            @error('email')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <button 
                type="submit" 
                class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150"
            >
                Send Password Reset Request
            </button>
        </div>

        <div class="text-center pt-2">
            <a href="{{ route('login') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Sign In
            </a>
        </div>
    </form>
@endsection
