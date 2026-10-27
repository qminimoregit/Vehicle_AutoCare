@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <x-page-header 
        title="Employees" 
        description="Manage employee profiles, daily wage rates, and employment status."
    >
        <x-slot:actions>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5"></span>
                    Active: {{ $activeCount }}
                </span>
                @if($inactiveCount > 0)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                        Inactive: {{ $inactiveCount }}
                    </span>
                @endif

                @if(Auth::user()->isAdmin())
                    <a 
                        href="{{ route('payroll.employees.create') }}" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Employee
                    </a>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('payroll.employees.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search Employee by name, ID (e.g. EMP-0001), mobile, position..." 
                    class="block w-full pl-9 pr-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                >
            </div>

            <div class="w-full sm:w-48">
                <select 
                    name="status" 
                    onchange="this.form.submit()"
                    class="block w-full py-2 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-[#0B1849] text-white text-sm font-semibold rounded-lg hover:bg-[#15286e] transition shrink-0"
                >
                    Search
                </button>
                @if($search || $status !== 'all')
                    <a 
                        href="{{ route('payroll.employees.index') }}" 
                        class="px-3 py-2 bg-gray-100 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-200 transition shrink-0"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Desktop Employee Table -->
    <div class="hidden md:block bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        @if($employees->count() > 0)
            <div class="responsive-table-wrapper">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Employee</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mobile Number</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Position</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Salary / Wage</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($employees as $emp)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-9 h-9 rounded-full bg-[#EEF1FA] text-[#0B1849] font-bold text-sm flex items-center justify-center mr-3 shrink-0">
                                            {{ strtoupper(substr($emp->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('payroll.employees.show', $emp) }}" class="text-sm font-semibold text-gray-900 hover:text-[#0B1849] hover:underline">
                                                {{ $emp->name }}
                                            </a>
                                            <div class="text-xs text-gray-500 font-mono">
                                                {{ $emp->employee_number }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $emp->mobile ?: '—' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $emp->position ?: 'General Staff' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    @if(($emp->salary_type ?? 'daily') === 'monthly')
                                        <div class="text-sm font-semibold text-gray-900 font-mono">
                                            LKR {{ number_format($emp->monthly_basic_salary, 2) }}
                                        </div>
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Monthly
                                        </span>
                                    @else
                                        <div class="text-sm font-semibold text-gray-900 font-mono">
                                            LKR {{ number_format($emp->daily_wage, 2) }}
                                        </div>
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Daily
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    @if($emp->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a 
                                        href="{{ route('payroll.employees.show', $emp) }}" 
                                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-md border border-gray-200 transition"
                                        title="View Details"
                                    >
                                        View
                                    </a>

                                    @if(Auth::user()->isAdmin())
                                        <a 
                                            href="{{ route('payroll.employees.edit', $emp) }}" 
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-[#0B1849] bg-[#EEF1FA] hover:bg-[#dfe4f5] rounded-md transition"
                                            title="Edit Employee"
                                        >
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('payroll.employees.toggle-status', $emp) }}" class="inline-block" onsubmit="return confirm('Change status for {{ $emp->name }}?')">
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium {{ $emp->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }} rounded-md transition cursor-pointer"
                                            >
                                                {{ $emp->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($employees->hasPages())
                <div class="p-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $employees->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-gray-900">No employees found</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                    @if($search)
                        No employee profiles match your search criteria. Try a different name or ID.
                    @else
                        Get started by registering your first employee in the system.
                    @endif
                </p>
                @if(Auth::user()->isAdmin() && !$search)
                    <div class="mt-4">
                        <a 
                            href="{{ route('payroll.employees.create') }}" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-xs rounded-lg shadow-xs transition"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Register Employee
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Mobile Employee Cards View -->
    <div class="block md:hidden space-y-3">
        @forelse($employees as $emp)
            <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-xs space-y-3">
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-[#EEF1FA] text-[#0B1849] font-bold text-sm flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($emp->name, 0, 1)) }}
                        </div>
                        <div>
                            <a href="{{ route('payroll.employees.show', $emp) }}" class="text-base font-semibold text-gray-900">
                                {{ $emp->name }}
                            </a>
                            <div class="text-xs text-gray-500 font-mono">
                                {{ $emp->employee_number }} &bull; {{ $emp->position ?: 'Staff' }}
                            </div>
                        </div>
                    </div>
                    @if($emp->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                            Inactive
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs py-2 border-y border-gray-100 bg-slate-50/50 rounded-lg px-3">
                    <div>
                        <span class="text-gray-500 block">
                            {{ ($emp->salary_type ?? 'daily') === 'monthly' ? 'Monthly Salary' : 'Daily Wage' }}
                        </span>
                        <span class="font-bold text-gray-900 font-mono">
                            LKR {{ number_format(($emp->salary_type ?? 'daily') === 'monthly' ? $emp->monthly_basic_salary : $emp->daily_wage, 2) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Mobile</span>
                        <span class="font-medium text-gray-900">{{ $emp->mobile ?: '—' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-1">
                    <a 
                        href="{{ route('payroll.employees.show', $emp) }}" 
                        class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                    >
                        View Profile
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a 
                            href="{{ route('payroll.employees.edit', $emp) }}" 
                            class="px-3 py-1.5 text-xs font-semibold text-[#0B1849] bg-[#EEF1FA] hover:bg-[#dfe4f5] rounded-lg transition"
                        >
                            Edit
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                <p class="text-sm font-medium">No employees found.</p>
            </div>
        @endforelse

        @if($employees->hasPages())
            <div class="pt-2">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
