@extends('layouts.app')

@section('title', 'Edit Vehicle Type')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('master-data.vehicle-types.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2">
                &larr; Back to Vehicle Types
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Edit Vehicle Type</h1>
            <p class="mt-1 text-sm text-gray-500">Update vehicle classification details and image.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form 
                method="POST" 
                action="{{ route('master-data.vehicle-types.update', $vehicleType) }}" 
                enctype="multipart/form-data" 
                class="space-y-6" 
                novalidate
                x-data="{
                    newPreviewUrl: null,
                    fileName: '',
                    handleFileSelect(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.fileName = file.name;
                            this.newPreviewUrl = URL.createObjectURL(file);
                        } else {
                            this.clearNewImage();
                        }
                    },
                    clearNewImage() {
                        this.newPreviewUrl = null;
                        this.fileName = '';
                        this.$refs.fileInput.value = '';
                    }
                }"
            >
                @csrf
                @method('PUT')

                <!-- Vehicle Type Name -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                        Vehicle Type Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="mt-1.5">
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            required 
                            value="{{ old('name', $vehicleType->name) }}" 
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Image -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Current Image:
                    </label>
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 border border-gray-200 rounded-xl">
                        @if($vehicleType->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($vehicleType->image))
                            <div class="w-20 h-20 rounded-lg bg-white border border-gray-200 p-1 flex items-center justify-center overflow-hidden shadow-2xs">
                                <img 
                                    src="{{ asset('storage/' . $vehicleType->image) }}" 
                                    alt="{{ $vehicleType->name }}" 
                                    class="w-full h-full object-contain"
                                />
                            </div>
                            <div class="text-xs text-gray-600">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-1">
                                    Active image assigned
                                </span>
                                <p class="text-gray-500">This image will be kept if no new file is uploaded.</p>
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-lg bg-gray-100 border border-dashed border-gray-300 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17a2 2 0 100-4 2 2 0 000 4zm8 0a2 2 0 100-4 2 2 0 000 4zM4 11h16M4 11l2-5h12l2 5M4 11v6h2m12 0h2v-6" />
                                </svg>
                                <span class="text-[9px] uppercase tracking-wider font-semibold text-gray-400 mt-1">No image</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <p>No vehicle image is currently attached to this type.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Change Image -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Change Image: <span class="text-gray-400 font-normal normal-case">(Optional - upload only if you wish to replace the current image)</span>
                    </label>

                    <div class="space-y-3">
                        <input 
                            type="file" 
                            name="image" 
                            id="image" 
                            x-ref="fileInput"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            @change="handleFileSelect($event)"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 file:cursor-pointer border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50"
                        />

                        <!-- Newly Selected Image Preview Box -->
                        <div x-show="newPreviewUrl" x-cloak class="p-4 bg-blue-50/60 border border-blue-200 rounded-xl">
                            <div class="flex items-start justify-between">
                                <span class="text-xs font-bold text-blue-900 uppercase tracking-wider">New Image Selected for Replacement:</span>
                                <button 
                                    type="button" 
                                    @click="clearNewImage()" 
                                    class="text-xs text-rose-600 hover:text-rose-800 font-medium cursor-pointer"
                                >
                                    Cancel replacement
                                </button>
                            </div>
                            <div class="mt-2 flex items-center space-x-4">
                                <div class="w-20 h-20 rounded-lg bg-white border border-blue-200 p-1 flex items-center justify-center overflow-hidden shadow-2xs">
                                    <img :src="newPreviewUrl" alt="Replacement preview" class="w-full h-full object-contain" />
                                </div>
                                <div class="text-xs text-blue-900">
                                    <p class="font-medium truncate max-w-xs" x-text="fileName"></p>
                                    <p class="text-blue-700/80 mt-0.5">Will replace the previous image upon saving.</p>
                                </div>
                            </div>
                        </div>

                        @error('image')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Carpet Configuration Section -->
                <div class="pt-4 border-t border-gray-100" x-data="{
                    enableCarpets: {{ old('enable_carpets', $vehicleType->enable_carpets ? 'true' : 'false') }},
                    newCategoryName: '',
                    isAddingCategory: false,
                    categories: {{ json_encode($availableCarpetCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
                    selectedCategories: {{ json_encode(old('carpet_categories', $vehicleType->carpetCategories->pluck('id')->toArray())) }},

                    async addNewCategory() {
                        let name = this.newCategoryName.trim();
                        if (!name) return;
                        this.isAddingCategory = true;
                        try {
                            let res = await fetch('{{ route('master-data.vehicle-types.carpet-categories.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ name: name })
                            });
                            let data = await res.json();
                            if (data.success && data.category) {
                                if (!this.categories.some(c => c.id === data.category.id)) {
                                    this.categories.push(data.category);
                                }
                                if (!this.selectedCategories.includes(data.category.id)) {
                                    this.selectedCategories.push(data.category.id);
                                }
                                this.newCategoryName = '';
                            } else {
                                alert(data.message || 'Error adding carpet category');
                            }
                        } catch (e) {
                            console.error(e);
                            alert('Failed to connect to server.');
                        } finally {
                            this.isAddingCategory = false;
                        }
                    }
                }">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                Carpet Details Configuration
                            </label>
                            <p class="text-xs text-gray-500 mt-0.5">Configure whether this vehicle type supports carpet tracking during registration.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="enable_carpets" 
                                value="1" 
                                x-model="enableCarpets"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0B1849]"></div>
                            <span class="ml-2.5 text-xs font-bold text-gray-800" x-text="enableCarpets ? 'Enabled' : 'Disabled'"></span>
                        </label>
                    </div>

                    <!-- Applicable Categories (Visible when enableCarpets is true) -->
                    <div x-show="enableCarpets" x-cloak class="mt-4 p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                                Applicable Carpet Categories:
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                <template x-for="cat in categories" :key="cat.id">
                                    <label class="flex items-center p-2.5 bg-white border border-gray-200 rounded-lg hover:border-gray-300 cursor-pointer transition select-none">
                                        <input 
                                            type="checkbox" 
                                            name="carpet_categories[]" 
                                            :value="cat.id" 
                                            x-model="selectedCategories"
                                            class="h-4 w-4 text-[#0B1849] focus:ring-[#0B1849] border-gray-300 rounded cursor-pointer"
                                        >
                                        <span class="ml-2 text-xs font-semibold text-gray-800" x-text="cat.name"></span>
                                    </label>
                                </template>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-2">
                                Select the carpet categories applicable to this vehicle type (e.g. Front, Rear, Boot).
                            </p>
                        </div>

                        <!-- Inline Quick-Add New Carpet Category -->
                        <div class="pt-3 border-t border-gray-200/80">
                            <span class="block text-xs font-semibold text-gray-600 mb-1.5">+ Add New Carpet Category Name:</span>
                            <div class="flex items-center gap-2 max-w-md">
                                <input 
                                    type="text" 
                                    x-model="newCategoryName" 
                                    @keydown.enter.prevent="addNewCategory()"
                                    placeholder="e.g. Third Row Carpet or Custom Mat"
                                    class="block flex-1 px-3 py-1.5 text-xs text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-[#0B1849] bg-white"
                                >
                                <button 
                                    type="button" 
                                    @click="addNewCategory()"
                                    :disabled="isAddingCategory || !newCategoryName.trim()"
                                    class="px-3.5 py-1.5 bg-[#0B1849] text-white text-xs font-semibold rounded-lg hover:bg-[#15286e] disabled:opacity-50 transition cursor-pointer"
                                >
                                    <span x-text="isAddingCategory ? 'Adding...' : 'Add'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons: [Cancel] [Save Changes] -->
                <div class="pt-6 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a 
                        href="{{ route('master-data.vehicle-types.index') }}" 
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
