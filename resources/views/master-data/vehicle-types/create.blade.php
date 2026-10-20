@extends('layouts.app')

@section('title', 'Add Vehicle Type')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('master-data.vehicle-types.index') }}" class="text-xs font-semibold text-[#0B1849] hover:underline inline-flex items-center mb-2">
                &larr; Back to Vehicle Types
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Add Vehicle Type</h1>
            <p class="mt-1 text-sm text-gray-500">Create a new vehicle type with an optional reference image.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-6 sm:p-8 shadow-xs">
            <form 
                method="POST" 
                action="{{ route('master-data.vehicle-types.store') }}" 
                enctype="multipart/form-data" 
                class="space-y-6" 
                novalidate
                x-data="{
                    previewUrl: null,
                    fileName: '',
                    handleFileSelect(event) {
                        const file = event.target.files[0];
                        if (file) {
                            this.fileName = file.name;
                            this.previewUrl = URL.createObjectURL(file);
                        } else {
                            this.clearImage();
                        }
                    },
                    clearImage() {
                        this.previewUrl = null;
                        this.fileName = '';
                        this.$refs.fileInput.value = '';
                    }
                }"
            >
                @csrf

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
                            value="{{ old('name') }}" 
                            placeholder="e.g. Luxury Jeep, Large Van, Marcopolo Bus"
                            class="block w-full px-3.5 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] @error('name') border-rose-400 bg-rose-50/30 @enderror"
                        />
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Vehicle Image -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Vehicle Image <span class="text-gray-400 font-normal normal-case">(Optional - JPG, JPEG, PNG, WEBP up to 2MB)</span>
                    </label>

                    <!-- File Upload Control & Preview Container -->
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <input 
                                type="file" 
                                name="image" 
                                id="image" 
                                x-ref="fileInput"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                @change="handleFileSelect($event)"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 file:cursor-pointer border border-gray-300 rounded-lg cursor-pointer bg-gray-50/50"
                            />
                        </div>

                        <!-- Image Preview Box -->
                        <div x-show="previewUrl" x-cloak class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                            <div class="flex items-start justify-between">
                                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Image Preview:</span>
                                <button 
                                    type="button" 
                                    @click="clearImage()" 
                                    class="text-xs text-rose-600 hover:text-rose-800 font-medium cursor-pointer"
                                >
                                    Remove
                                </button>
                            </div>
                            <div class="mt-2 flex items-center space-x-4">
                                <div class="w-24 h-24 rounded-lg bg-white border border-gray-200 p-1 flex items-center justify-center overflow-hidden shadow-2xs">
                                    <img :src="previewUrl" alt="Vehicle preview" class="w-full h-full object-contain" />
                                </div>
                                <div class="text-xs text-gray-600">
                                    <p class="font-medium text-gray-900 truncate max-w-xs" x-text="fileName"></p>
                                    <p class="text-gray-400 mt-0.5">Ready to upload on save</p>
                                </div>
                            </div>
                        </div>

                        @error('image')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons: [Cancel] [Save Vehicle Type] -->
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
                        Save Vehicle Type
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
