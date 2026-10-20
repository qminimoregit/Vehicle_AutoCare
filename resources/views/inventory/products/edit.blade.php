@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('inventory.products.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2">
                &larr; Back to Products
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Edit Product</h1>
            <p class="mt-1 text-sm text-gray-500">Update catalogue details and pricing for <span class="font-semibold text-gray-800">{{ $product->name }}</span>.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('inventory.products.update', $product) }}" class="space-y-6" novalidate>
                @csrf
                @method('PUT')

                <!-- Product Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Product Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name', $product->name) }}" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Product Code / SKU & UOM -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="sku" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Product Code / SKU <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="text" 
                                name="sku" 
                                id="sku" 
                                value="{{ old('sku', $product->sku) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono @error('sku') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('sku')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="uom_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Unit of Measurement (UOM) <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5">
                            <select 
                                name="uom_id" 
                                id="uom_id" 
                                required
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white @error('uom_id') border-rose-400 bg-rose-50/30 @enderror"
                            >
                                @foreach($uoms as $uom)
                                    <option value="{{ $uom->id }}" {{ old('uom_id', $product->uom_id) == $uom->id ? 'selected' : '' }}>
                                        {{ $uom->name }} ({{ $uom->code ?: 'Unit' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('uom_id')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Preferred Supplier & Selling Price -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="preferred_supplier_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Preferred Supplier <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <select 
                                name="preferred_supplier_id" 
                                id="preferred_supplier_id" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                            >
                                <option value="">No Preferred Supplier (Any)</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('preferred_supplier_id', $product->preferred_supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="selling_price" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Selling Price (LKR) <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1.5 relative rounded-lg shadow-2xs">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs text-gray-500 font-semibold">
                                Rs.
                            </div>
                            <input 
                                type="number" 
                                step="0.01" 
                                min="0" 
                                name="selling_price" 
                                id="selling_price" 
                                required 
                                value="{{ old('selling_price', $product->selling_price) }}" 
                                class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono @error('selling_price') border-rose-400 bg-rose-50/30 @enderror"
                            />
                        </div>
                        @error('selling_price')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Stock Limits: Min & Max Stock -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="min_stock" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Minimum Stock Level <span class="text-gray-400 font-normal normal-case">(Reorder Alert)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="number" 
                                step="1" 
                                min="0" 
                                name="min_stock" 
                                id="min_stock" 
                                value="{{ old('min_stock', $product->min_stock) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono"
                            />
                        </div>
                        @error('min_stock')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_stock" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                            Maximum Stock Level <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                        </label>
                        <div class="mt-1.5">
                            <input 
                                type="number" 
                                step="1" 
                                min="0" 
                                name="max_stock" 
                                id="max_stock" 
                                value="{{ old('max_stock', $product->max_stock) }}" 
                                class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] font-mono"
                            />
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Description / Specifications <span class="text-gray-400 font-normal normal-case">(Optional)</span>
                    </label>
                    <div class="mt-1.5">
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="2" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849]"
                        >{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <select 
                            name="is_active" 
                            id="is_active" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] bg-white"
                        >
                            <option value="1" {{ old('is_active', $product->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $product->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons: [Cancel] [Save Changes] -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('inventory.products.index') }}" 
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-2xs text-center"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B1849] hover:bg-[#15286e] rounded-lg shadow-xs transition cursor-pointer"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
