@extends('layouts.app')

@section('title', 'Edit Staff Account - ' . $staff->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.staff.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center mb-2">
                &larr; Back to Staff Accounts
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Edit Staff Account</h1>
            <p class="mt-1 text-sm text-gray-500">Update account profile for: <span class="font-semibold text-gray-800">{{ $staff->name }}</span>.</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('admin.staff.update', $staff) }}" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name', $staff->name) }}" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            required 
                            value="{{ old('email', $staff->email) }}" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('email') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Phone Number <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <div class="mt-1">
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $staff->phone) }}" 
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('phone') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            System Role <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select 
                                name="role" 
                                id="role" 
                                required
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                            >
                                <option value="staff" {{ old('role', $staff->role) === 'staff' ? 'selected' : '' }}>Staff (Operational Access)</option>
                                <option value="admin" {{ old('role', $staff->role) === 'admin' ? 'selected' : '' }}>Administrator (Full Access)</option>
                            </select>
                        </div>
                        @error('role')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Account Status <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select 
                                name="status" 
                                id="status" 
                                required
                                class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                            >
                                <option value="active" {{ old('status', $staff->status) === 'active' ? 'selected' : '' }}>Active (Can Sign In)</option>
                                <option value="inactive" {{ old('status', $staff->status) === 'inactive' ? 'selected' : '' }}>Inactive (Access Blocked)</option>
                            </select>
                        </div>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Password (Optional on update) -->
                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        New Password <span class="text-gray-400 font-normal">(Leave blank to keep current)</span>
                    </label>
                    <div class="mt-1">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            placeholder="Only enter if changing password (min 8 chars)"
                            class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 @error('password') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('admin.staff.index') }}" 
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
