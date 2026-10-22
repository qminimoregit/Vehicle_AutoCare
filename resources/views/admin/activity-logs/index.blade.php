@extends('layouts.app')

@section('title', 'System Activity Logs')

@section('content')
    <!-- Page Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">System Activity Logs</h1>
            <p class="mt-1 text-sm text-gray-500">Security audit trail of user actions, master data updates, and authentication events.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                Audit Trail Active
            </span>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="flex flex-col sm:flex-row gap-3">
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
                    placeholder="Search logs by description, action, or IP address..." 
                    class="block w-full pl-9 pr-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600"
                />
            </div>

            <div class="w-full sm:w-48">
                <select 
                    name="module" 
                    onchange="this.form.submit()"
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-blue-600 bg-white"
                >
                    <option value="">All Modules</option>
                    <option value="Services" {{ request('module') === 'Services' ? 'selected' : '' }}>Services</option>
                    <option value="Vehicle Types" {{ request('module') === 'Vehicle Types' ? 'selected' : '' }}>Vehicle Types</option>
                    <option value="Company Details" {{ request('module') === 'Company Details' ? 'selected' : '' }}>Company Details</option>
                    <option value="Staff Management" {{ request('module') === 'Staff Management' ? 'selected' : '' }}>Staff Management</option>
                    <option value="Authentication" {{ request('module') === 'Authentication' ? 'selected' : '' }}>Authentication</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 rounded-lg transition">
                    Search
                </button>
                @if(request('search') || request('module'))
                    <a href="{{ route('admin.activity-logs.index') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activity Log Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/80">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Timestamp
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            User
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Module / Action
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                            IP Address
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                <div>{{ $log->created_at ? $log->created_at->setTimezone('Asia/Colombo')->format('Y-m-d H:i') : '' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-semibold text-gray-900">
                                    {{ $log->user ? $log->user->name : 'System Console' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ $log->module ?: 'General' }}
                                </span>
                                <span class="ml-1 text-[11px] font-mono text-gray-500">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-xs text-gray-700 max-w-md">
                                {{ $log->description }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-mono text-gray-400">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No activity logs recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
@endsection
