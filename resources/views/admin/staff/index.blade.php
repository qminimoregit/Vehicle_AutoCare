@extends('layouts.app')

@section('title', 'Staff Accounts')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Staff Accounts Management</h1>
            <p class="mt-1 text-sm text-gray-500">Create, manage, and configure access permissions for center employees and managers.</p>
        </div>
        <div>
            <a 
                href="{{ route('admin.staff.create') }}" 
                class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-lg shadow-xs text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Create Staff Account
            </a>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="flex flex-col sm:flex-row gap-3">
            <!-- Search Input -->
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by name, email, or phone number..." 
                    class="block w-full pl-9 pr-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                />
            </div>

            <!-- Role Filter -->
            <div class="w-full sm:w-40">
                <select 
                    name="role" 
                    onchange="this.form.submit()"
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                >
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-full sm:w-40">
                <select 
                    name="status" 
                    onchange="this.form.submit()"
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                >
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition">
                    Search
                </button>
                @if(request('search') || request('role') || request('status'))
                    <a href="{{ route('admin.staff.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Employee / User
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Role & Access
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-white font-bold text-xs flex items-center justify-center uppercase">
                                        {{ substr($user->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 flex items-center space-x-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === Auth::id())
                                                <span class="text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.2 rounded font-semibold">You</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $user->email }} @if($user->phone) &bull; {{ $user->phone }} @endif</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$user->role" />
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <x-status-badge :status="$user->status" />
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="inline-flex items-center space-x-2">
                                    <a 
                                        href="{{ route('admin.staff.edit', $user) }}" 
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 transition"
                                    >
                                        Edit
                                    </a>

                                    @if($user->id !== Auth::id())
                                        <form 
                                            method="POST" 
                                            action="{{ route('admin.staff.toggle-status', $user) }}" 
                                            onsubmit="return confirm('Are you sure you want to {{ $user->isActive() ? 'deactivate' : 'activate' }} {{ $user->name }}?')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            @if($user->isActive())
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                No staff accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
