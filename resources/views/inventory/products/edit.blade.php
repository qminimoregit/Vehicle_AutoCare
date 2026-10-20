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
            <form method="POST" action="{{ route('inventory.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6" novalidate>
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

                <!-- Product Image -->
                <div x-data="{
                    imagePreview: null,
                    hasExisting: {{ $product->image_url ? 'true' : 'false' }},
                    removeExisting: false,
                    fileChosen(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        this.removeExisting = false;
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    },
                    clearNew() {
                        this.imagePreview = null;
                        this.$refs.imageInput.value = '';
                    },
                    markRemove() {
                        this.removeExisting = true;
                        this.clearNew();
                    },
                    undoRemove() {
                        this.removeExisting = false;
                    }
                }">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Product Image <span class="text-gray-400 font-normal normal-case">(JPG, JPEG, PNG, WEBP - Max 2MB)</span>
                    </label>

                    <input type="hidden" name="remove_image" :value="removeExisting ? '1' : '0'">

                    <div class="mt-2 flex items-center space-x-4">
                        <!-- Preview Thumbnail / Current Image / Placeholder -->
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0 relative shadow-2xs">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" alt="New Preview" class="w-full h-full object-contain p-1" />
                            </template>
                            <template x-if="!imagePreview && hasExisting && !removeExisting">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-1" />
                            </template>
                            <template x-if="(!imagePreview && !hasExisting) || (!imagePreview && removeExisting)">
                                <div class="text-center p-2 text-gray-400">
                                    <svg class="mx-auto h-7 w-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px] block font-medium mt-0.5" x-text="removeExisting ? 'Removed' : 'No image'"></span>
                                </div>
                            </template>
                        </div>

                        <!-- Upload controls -->
                        <div class="flex-1 space-y-1.5">
                            <input 
                                type="file" 
                                name="image" 
                                id="image" 
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                x-ref="imageInput"
                                @change="fileChosen"
                                class="block w-full text-xs text-gray-600 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#EEF1FA] file:text-[#0B1849] hover:file:bg-[#dce4f7] cursor-pointer"
                            />
                            <div class="flex items-center justify-between text-[11px] text-gray-500">
                                <span>Choose a file to replace existing image, or clear</span>
                                <div class="space-x-2">
                                    <button 
                                        type="button" 
                                        x-show="imagePreview" 
                                        @click="clearNew" 
                                        class="text-amber-600 hover:text-amber-800 font-semibold cursor-pointer"
                                    >
                                        Cancel new file
                                    </button>
                                    <button 
                                        type="button" 
                                        x-show="hasExisting && !removeExisting && !imagePreview" 
                                        @click="markRemove" 
                                        class="text-rose-600 hover:text-rose-800 font-semibold cursor-pointer"
                                    >
                                        Remove image
                                    </button>
                                    <button 
                                        type="button" 
                                        x-show="removeExisting" 
                                        @click="undoRemove" 
                                        class="text-blue-600 hover:text-blue-800 font-semibold cursor-pointer"
                                    >
                                        Keep image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('image')
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
                                step="{{ $product->quantity_step }}" 
                                min="0" 
                                name="min_stock" 
                                id="min_stock" 
                                value="{{ old('min_stock', \App\Services\QuantityFormatter::format($product->min_stock, null, false)) }}" 
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
                                step="{{ $product->quantity_step }}" 
                                min="0" 
                                name="max_stock" 
                                id="max_stock" 
                                value="{{ old('max_stock', $product->max_stock ? \App\Services\QuantityFormatter::format($product->max_stock, null, false) : '') }}" 
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
