@props([
    'name',
    'id' => null,
    'value' => null,
    'placeholder' => 'Select an option...',
    'searchPlaceholder' => 'Search by name, code or keyword...',
    'endpoint' => null,
    'options' => [],
    'required' => false,
    'disabled' => false,
    'clearable' => true,
    'selectedLabel' => null,
    'selectedSubtext' => null,
    'autoSubmit' => false,
    'class' => '',
])

@php
    $elementId = $id ?? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
    $initialValue = old($name, $value);
    
    // Normalize options if passed as a Collection or model array
    $normalizedOptions = [];
    if (!empty($options)) {
        foreach ($options as $opt) {
            if (is_object($opt)) {
                $optId = $opt->id ?? $opt->value ?? null;
                $optLabel = $opt->name ?? $opt->label ?? $opt->customer_name ?? $opt->vehicle_number ?? (string)$optId;
                $optSub = $opt->subtext ?? $opt->phone ?? $opt->mobile ?? $opt->sku ?? $opt->code ?? null;
            } elseif (is_array($opt)) {
                $optId = $opt['id'] ?? $opt['value'] ?? null;
                $optLabel = $opt['label'] ?? $opt['name'] ?? (string)$optId;
                $optSub = $opt['subtext'] ?? null;
            } else {
                $optId = $opt;
                $optLabel = (string)$opt;
                $optSub = null;
            }
            if ($optId !== null) {
                $normalizedOptions[] = [
                    'id' => (string)$optId,
                    'label' => (string)$optLabel,
                    'subtext' => $optSub ? (string)$optSub : null,
                ];
            }
        }
    }

    // Attempt to resolve selectedLabel from options if not explicitly provided
    if ($selectedLabel === null && !empty($initialValue) && !empty($normalizedOptions)) {
        foreach ($normalizedOptions as $opt) {
            if ((string)$opt['id'] === (string)$initialValue) {
                $selectedLabel = $opt['label'];
                $selectedSubtext = $opt['subtext'] ?? null;
                break;
            }
        }
    }
@endphp

<div 
    {{ $attributes->merge(['class' => 'relative w-full ' . $class]) }}
    x-data="searchableSelect({
        name: @js($name),
        elementId: @js($elementId),
        endpoint: @js($endpoint),
        localOptions: @js($normalizedOptions),
        initialValue: @js((string)$initialValue),
        initialLabel: @js($selectedLabel ?? ''),
        initialSubtext: @js($selectedSubtext ?? ''),
        required: @js($required),
        disabled: @js($disabled),
        clearable: @js($clearable),
        autoSubmit: @js($autoSubmit)
    })"
    @keydown.escape.window="close()"
    data-searchable-select="{{ $name }}"
>
    <!-- Hidden Form Input that Submits the Actual Database ID -->
    <input 
        type="hidden" 
        name="{{ $name }}" 
        id="{{ $elementId }}" 
        :value="selectedValue"
        x-model="selectedValue" 
        {{ $required ? 'required' : '' }}
        {{ $attributes->whereStartsWith('wire:model') }}
    />

    <!-- Selection Trigger Button -->
    <div 
        role="combobox"
        @click="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.space.prevent="toggle()"
        @keydown.arrow-down.prevent="openAndFocus()"
        tabindex="{{ $disabled ? -1 : 0 }}"
        class="w-full flex items-center justify-between px-3.5 py-2 text-sm bg-white border rounded-lg shadow-2xs transition select-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] min-h-[40px]"
        :class="{
            'border-gray-300 hover:border-gray-400': !isOpen && !disabled,
            'border-[#0B1849] ring-2 ring-[#0B1849]/20': isOpen,
            'opacity-60 bg-gray-100 cursor-not-allowed pointer-events-none': disabled
        }"
        aria-haspopup="listbox"
        :aria-expanded="isOpen"
    >
        <div class="flex items-center min-w-0 flex-1 mr-2">
            <!-- Selected Item Label -->
            <template x-if="selectedValue">
                <div class="truncate text-left">
                    <span class="font-semibold text-gray-900 block truncate" x-text="selectedLabel"></span>
                    <span x-show="selectedSubtext" class="text-[11px] text-gray-500 block truncate" x-text="selectedSubtext"></span>
                </div>
            </template>

            <!-- Placeholder when nothing is selected -->
            <template x-if="!selectedValue">
                <span class="text-gray-400 text-sm truncate">{{ $placeholder }}</span>
            </template>
        </div>

        <div class="flex items-center gap-1.5 shrink-0 text-gray-400">
            <!-- Clear Button -->
            <button 
                type="button" 
                x-show="clearable && selectedValue && !disabled" 
                @click.stop="clear()" 
                class="p-1 hover:text-rose-600 rounded-md hover:bg-gray-100 transition cursor-pointer"
                title="Clear selection"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Dropdown Chevron Icon -->
            <svg 
                :class="isOpen ? 'rotate-180 text-[#0B1849]' : 'text-gray-400'" 
                class="w-4 h-4 transition-transform duration-200" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    <!-- Dropdown Menu Panel -->
    <div 
        x-show="isOpen" 
        x-cloak 
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        @click.outside="close()"
        class="absolute left-0 right-0 top-full mt-1.5 z-50 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden min-w-[240px]"
        style="display: none;"
    >
        <!-- Search Input Bar -->
        <div class="p-2 border-b border-gray-100 bg-gray-50/70">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input 
                    type="text" 
                    x-ref="searchInput"
                    x-model="searchQuery" 
                    @input.debounce.250ms="onSearchInput()"
                    @keydown.down.prevent="navigateDown()"
                    @keydown.up.prevent="navigateUp()"
                    @keydown.enter.prevent="selectHighlighted()"
                    @keydown.escape.prevent="close()"
                    placeholder="{{ $searchPlaceholder }}"
                    class="block w-full pl-9 pr-8 py-2 text-xs sm:text-sm text-gray-900 placeholder-gray-400 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0B1849] focus:border-[#0B1849] shadow-2xs"
                />
                <!-- Search Clear button -->
                <button 
                    type="button" 
                    x-show="searchQuery" 
                    @click="searchQuery = ''; onSearchInput(); $refs.searchInput.focus();" 
                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-gray-400 hover:text-gray-600 cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Options Results List -->
        <div 
            class="max-h-60 overflow-y-auto divide-y divide-gray-50 p-1 text-sm focus:outline-none"
            role="listbox"
            tabindex="-1"
        >
            <!-- Loading State -->
            <div x-show="loading" class="py-6 text-center text-xs text-gray-500 flex items-center justify-center gap-2">
                <svg class="animate-spin h-4 w-4 text-[#0B1849]" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Searching records...</span>
            </div>

            <!-- List Items -->
            <template x-for="(item, index) in filteredItems" :key="item.id">
                <div 
                    @click="selectItem(item)" 
                    @mouseenter="highlightedIndex = index"
                    :class="{
                        'bg-[#EEF1FA] text-[#0B1849] font-semibold': highlightedIndex === index,
                        'text-gray-800 hover:bg-gray-50': highlightedIndex !== index,
                        'bg-blue-50/50': String(item.id) === String(selectedValue)
                    }"
                    class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between transition gap-2"
                    role="option"
                    :aria-selected="String(item.id) === String(selectedValue)"
                >
                    <div class="min-w-0 flex-1">
                        <span class="block truncate text-xs sm:text-sm font-medium" x-text="item.label"></span>
                        <span x-show="item.subtext" class="block truncate text-[11px] text-gray-500 font-normal" x-text="item.subtext"></span>
                    </div>

                    <!-- Selected Checkmark -->
                    <template x-if="String(item.id) === String(selectedValue)">
                        <svg class="w-4 h-4 text-[#0B1849] shrink-0 font-bold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                </div>
            </template>

            <!-- Empty / No Results Found -->
            <div 
                x-show="!loading && filteredItems.length === 0" 
                class="py-6 text-center text-xs text-gray-400"
            >
                <svg class="w-6 h-6 mx-auto mb-1 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-medium text-gray-600">No matching records found</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Try searching with different keywords</p>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof window.searchableSelect !== 'function') {
    window.searchableSelect = function(config) {
        return {
            name: config.name,
            elementId: config.elementId,
            endpoint: config.endpoint,
            localOptions: config.localOptions || [],
            selectedValue: config.initialValue || '',
            selectedLabel: config.initialLabel || '',
            selectedSubtext: config.initialSubtext || '',
            required: config.required || false,
            disabled: config.disabled || false,
            clearable: config.clearable !== false,
            autoSubmit: config.autoSubmit || false,

            isOpen: false,
            searchQuery: '',
            filteredItems: [],
            loading: false,
            highlightedIndex: -1,

            init() {
                // If local options exist, initialize list
                if (this.localOptions.length > 0) {
                    this.filteredItems = this.localOptions;
                    if (this.selectedValue && !this.selectedLabel) {
                        let match = this.localOptions.find(o => String(o.id) === String(this.selectedValue));
                        if (match) {
                            this.selectedLabel = match.label;
                            this.selectedSubtext = match.subtext || '';
                        }
                    }
                } else if (this.endpoint && this.selectedValue && !this.selectedLabel) {
                    // Fetch initial label for pre-selected ID
                    this.fetchInitialLabel();
                }

                this.$watch('selectedValue', (newVal) => {
                    this.$dispatch('change', newVal);
                });
            },

            async fetchInitialLabel() {
                if (!this.endpoint || !this.selectedValue) return;
                try {
                    let url = new URL(this.endpoint, window.location.origin);
                    url.searchParams.set('id', this.selectedValue);
                    let res = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        let data = await res.json();
                        if (Array.isArray(data) && data.length > 0) {
                            this.selectedLabel = data[0].label;
                            this.selectedSubtext = data[0].subtext || '';
                        }
                    }
                } catch (e) {
                    console.error('Failed to resolve initial label for searchable select', e);
                }
            },

            toggle() {
                if (this.disabled) return;
                if (this.isOpen) {
                    this.close();
                } else {
                    this.openAndFocus();
                }
            },

            openAndFocus() {
                if (this.disabled) return;
                this.isOpen = true;
                this.searchQuery = '';
                this.highlightedIndex = -1;

                if (this.endpoint) {
                    this.fetchRemoteResults('');
                } else {
                    this.filterLocalResults('');
                }

                this.$nextTick(() => {
                    if (this.$refs.searchInput) {
                        this.$refs.searchInput.focus();
                    }
                });
            },

            close() {
                this.isOpen = false;
                this.highlightedIndex = -1;
            },

            onSearchInput() {
                if (this.endpoint) {
                    this.fetchRemoteResults(this.searchQuery);
                } else {
                    this.filterLocalResults(this.searchQuery);
                }
            },

            filterLocalResults(query) {
                let q = (query || '').toLowerCase().trim();
                if (!q) {
                    this.filteredItems = this.localOptions;
                } else {
                    this.filteredItems = this.localOptions.filter(item => {
                        let text = (item.label + ' ' + (item.subtext || '')).toLowerCase();
                        return text.includes(q);
                    });
                }
                this.highlightedIndex = this.filteredItems.length > 0 ? 0 : -1;
            },

            async fetchRemoteResults(query) {
                if (!this.endpoint) return;
                this.loading = true;
                try {
                    let url = new URL(this.endpoint, window.location.origin);
                    if (query) {
                        url.searchParams.set('q', query);
                    }
                    let res = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        let data = await res.json();
                        this.filteredItems = Array.isArray(data) ? data : [];
                    } else {
                        this.filteredItems = [];
                    }
                } catch (e) {
                    console.error('Remote search error', e);
                    this.filteredItems = [];
                } finally {
                    this.loading = false;
                    this.highlightedIndex = this.filteredItems.length > 0 ? 0 : -1;
                }
            },

            selectItem(item) {
                if (!item) return;
                this.selectedValue = String(item.id);
                this.selectedLabel = item.label;
                this.selectedSubtext = item.subtext || '';
                this.close();

                // Dispatch native custom events for Alpine and vanilla JS listeners
                this.$dispatch('selected', item);
                let hiddenInput = document.getElementById(this.elementId);
                if (hiddenInput) {
                    hiddenInput.value = String(item.id);
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }

                if (this.autoSubmit) {
                    this.$nextTick(() => {
                        let form = this.$el.closest('form');
                        if (form) form.submit();
                    });
                }
            },

            clear() {
                this.selectedValue = '';
                this.selectedLabel = '';
                this.selectedSubtext = '';
                this.close();
                this.$dispatch('cleared');
                let hiddenInput = document.getElementById(this.elementId);
                if (hiddenInput) {
                    hiddenInput.value = '';
                    hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
                if (this.autoSubmit) {
                    this.$nextTick(() => {
                        let form = this.$el.closest('form');
                        if (form) form.submit();
                    });
                }
            },

            navigateDown() {
                if (!this.isOpen) {
                    this.openAndFocus();
                    return;
                }
                if (this.filteredItems.length === 0) return;
                this.highlightedIndex = (this.highlightedIndex + 1) % this.filteredItems.length;
            },

            navigateUp() {
                if (!this.isOpen) {
                    this.openAndFocus();
                    return;
                }
                if (this.filteredItems.length === 0) return;
                this.highlightedIndex = (this.highlightedIndex - 1 + this.filteredItems.length) % this.filteredItems.length;
            },

            selectHighlighted() {
                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredItems.length) {
                    this.selectItem(this.filteredItems[this.highlightedIndex]);
                }
            }
        };
    };
}
</script>
