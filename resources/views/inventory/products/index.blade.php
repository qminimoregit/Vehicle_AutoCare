@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div>
        <!-- Page Header -->
        <x-page-header 
            title="Products" 
            description="Manage inventory product catalogue and selling prices."
        >
            <x-slot:actions>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                        Total: {{ $totalProducts }} {{ Str::plural('product', $totalProducts) }}
                    </span>

                    @if(Auth::user()->isAdmin())
                        <a 
                            href="{{ route('inventory.products.create') }}" 
                            class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                        >
                            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Product
                        </a>
                    @endif
                </div>
            </x-slot:actions>
        </x-page-header>

        <!-- Search Bar with Supplier Filter -->
        <x-search-bar 
            :action="route('inventory.products.index')" 
            :search="$search" 
            placeholder="Search Products by name, SKU..."
            :resetUrl="route('inventory.products.index')"
        >
            <div class="w-full sm:w-56">
                <select 
                    name="supplier_id" 
                    onchange="this.form.submit()"
                    class="block w-full py-2.5 px-3 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                >
                    <option value="">All Preferred Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </x-search-bar>

        <!-- Products Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Product
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-24">
                                UOM
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Preferred Supplier
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Sale Price (LKR)
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-24">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3.5 text-right text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50/70 transition">
                                <!-- Product Name & SKU -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $product->name }}</div>
                                    <div class="flex items-center space-x-2 mt-0.5 text-xs text-gray-500">
                                        @if($product->sku)
                                            <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">SKU: {{ $product->sku }}</span>
                                        @endif
                                        @if($product->description)
                                            <span class="truncate max-w-xs">{{ $product->description }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- UOM -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $product->uom ? $product->uom->name : '-' }}
                                    </span>
                                </td>

                                <!-- Preferred Supplier -->
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $product->preferredSupplier ? $product->preferredSupplier->name : '-' }}
                                </td>

                                <!-- Selling Price -->
                                <td class="px-6 py-4 whitespace-nowrap text-right font-mono font-bold text-sm text-gray-900">
                                    {{ number_format($product->selling_price, 2) }}
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($product->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-300">
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    @if(Auth::user()->isAdmin())
                                        <a 
                                            href="{{ route('inventory.products.edit', $product) }}" 
                                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md text-[#0B1849] bg-blue-50 hover:bg-blue-100 border border-blue-200 transition"
                                        >
                                            Edit
                                        </a>

                                        <form 
                                            method="POST" 
                                            action="{{ route('inventory.products.toggle-status', $product) }}" 
                                            class="inline-block"
                                            onsubmit="return confirm('Change status for {{ $product->name }}?')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold rounded-md {{ $product->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' }} transition cursor-pointer"
                                            >
                                                {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-400 italic">View Only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="No products found." 
                                        description="Start by adding items, oil filters, fluids, or consumable parts to your inventory."
                                    >
                                        @if(Auth::user()->isAdmin())
                                            <x-slot:action>
                                                <a 
                                                    href="{{ route('inventory.products.create') }}" 
                                                    class="inline-flex items-center justify-center px-4 py-2 bg-[#0B1849] hover:bg-[#15286e] text-white font-semibold text-sm rounded-lg shadow-xs transition duration-150 cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1849]"
                                                >
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add Product
                                                </a>
                                            </x-slot:action>
                                        @endif
                                    </x-empty-state>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
