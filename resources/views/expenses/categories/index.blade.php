@extends('layouts.app')

@section('title', 'Expense Categories')

@section('content')
<div x-data="{ 
    showAddModal: {{ $errors->any() && old('_form_type') === 'create' ? 'true' : 'false' }},
    showEditModal: {{ $errors->any() && old('_form_type') === 'edit' ? 'true' : 'false' }},
    editCategory: {
        id: '{{ old('_form_type') === 'edit' ? old('category_id') : '' }}',
        name: '{{ old('_form_type') === 'edit' ? old('name') : '' }}',
        is_active: '{{ old('_form_type') === 'edit' ? old('is_active', '1') : '1' }}'
    },
    openEdit(cat) {
        this.editCategory = {
            id: cat.id,
            name: cat.name,
            is_active: cat.is_active ? '1' : '0'
        };
        this.showEditModal = true;
    }
}">
    <!-- Page Header -->
    <x-page-header 
        title="Expense Categories" 
        description="Manage classifications for shop operating costs and staff payments."
    >
        <x-slot:actions>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                    Total: {{ $categories->total() }} {{ Str::plural('Category', $categories->total()) }}
                </span>
                @if(Auth::user()->isAdmin())
                    <button 
                        type="button" 
                        @click="showAddModal = true" 
                        class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                    >
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Category
                    </button>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Search Bar -->
    <div class="mb-6 bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <form method="GET" action="{{ route('expenses.categories.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Search Category..." 
                    class="block w-full pl-10 pr-3 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white placeholder-gray-400"
                >
            </div>
            <div class="flex items-center gap-2">
                <button 
                    type="submit" 
                    class="px-5 py-2.5 bg-[#0B1849] hover:bg-[#15286e] text-white text-sm font-semibold rounded-lg transition shadow-xs cursor-pointer"
                >
                    Search
                </button>
                @if(!empty($search))
                    <a 
                        href="{{ route('expenses.categories.index') }}" 
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Category List Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50/80 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">Category Name</th>
                        <th class="px-6 py-3.5">Linked Expenses</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $category->name }}
                                @if(strtolower(trim($category->name)) === 'salary advance')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Payroll Advance Link
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ $category->expenses_count }} {{ Str::plural('record', $category->expenses_count) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-gray-400"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                @if(Auth::user()->isAdmin())
                                    <button 
                                        type="button" 
                                        @click="openEdit({ id: '{{ $category->id }}', name: '{{ addslashes($category->name) }}', is_active: {{ $category->is_active ? 1 : 0 }} })"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md text-[#0B1849] bg-[#EEF1FA] hover:bg-[#d8e0f5] transition"
                                    >
                                        Edit
                                    </button>

                                    <form action="{{ route('expenses.categories.toggle-status', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Change status for category \'{{ addslashes($category->name) }}\'?');">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-md {{ $category->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }} transition"
                                        >
                                            {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="max-w-sm mx-auto">
                                    <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <h4 class="text-sm font-semibold text-gray-900">No expense categories found</h4>
                                    <p class="mt-1 text-xs text-gray-500">Add categories to organize your shop expenses and staff payments.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Info Note -->
    <div class="mt-4 p-4 rounded-xl bg-blue-50/60 border border-blue-100 text-xs text-blue-900 flex items-start space-x-2.5">
        <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>
            <strong>Note:</strong> Categories linked to existing transactions cannot be permanently deleted. Instead, set them to <strong>Inactive</strong> to hide them from new expense forms while preserving historical financial records.
        </span>
    </div>

    <!-- ADD CATEGORY MODAL -->
    <div 
        x-show="showAddModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showAddModal" 
                @click="showAddModal = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-xs"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div 
                x-show="showAddModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-gray-100"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Add Expense Category</h3>
                    <button @click="showAddModal = false" type="button" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('expenses.categories.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="_form_type" value="create">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Category Name *</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('_form_type') === 'create' ? old('name') : '' }}"
                            placeholder="e.g. Electricity, Water, Staff Welfare" 
                            required
                            class="block w-full px-3.5 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                        @if($errors->has('name') && old('_form_type') === 'create')
                            <p class="mt-1 text-xs text-red-600">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showAddModal = false" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition"
                        >
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div 
        x-show="showEditModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="showEditModal" 
                @click="showEditModal = false"
                class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-xs"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

            <div 
                x-show="showEditModal"
                class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-gray-100"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">Edit Category</h3>
                    <button @click="showEditModal = false" type="button" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="'{{ url('expenses/categories') }}/' + editCategory.id" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form_type" value="edit">
                    <input type="hidden" name="category_id" :value="editCategory.id">

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Category Name *</label>
                        <input 
                            type="text" 
                            name="name" 
                            x-model="editCategory.name" 
                            required
                            class="block w-full px-3.5 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >
                        @if($errors->has('name') && old('_form_type') === 'edit')
                            <p class="mt-1 text-xs text-red-600">{{ $errors->first('name') }}</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Status</label>
                        <select 
                            name="is_active" 
                            x-model="editCategory.is_active"
                            class="block w-full px-3.5 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-3 border-t border-gray-100">
                        <button 
                            type="button" 
                            @click="showEditModal = false" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition"
                        >
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
