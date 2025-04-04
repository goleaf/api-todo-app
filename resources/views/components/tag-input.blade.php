@props(['name' => 'tags', 'selected' => [], 'placeholder' => 'Search or create tags...', 'tags' => []])

<div
    x-data="tagInput({
        name: '{{ $name }}',
        selected: {{ json_encode(is_array($selected) ? $selected : []) }},
        placeholder: '{{ $placeholder }}',
        existingTags: {{ json_encode($tags) }}
    })"
    x-init="init()"
    class="relative"
>
    <!-- Hidden inputs for form submission -->
    <template x-for="tag in selected" :key="tag.id">
        <input type="hidden" :name="`${name}[]`" :value="tag.id">
        <template x-if="tag.id.toString().startsWith('new_')">
            <input type="hidden" :name="`new_tag_${tag.id}`" :value="tag.name">
        </template>
    </template>

    <!-- Main input field -->
    <div class="mt-1 flex rounded-md shadow-sm border border-gray-300 bg-white overflow-hidden">
        <div class="flex flex-wrap items-center p-1 w-full">
            <!-- Selected tags -->
            <template x-for="tag in selected" :key="tag.id">
                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1 mb-1">
                    <span x-text="tag.name"></span>
                    <button 
                        @click="removeTag(tag)"
                        type="button"
                        class="ml-1 inline-flex text-blue-400 hover:text-blue-500 focus:outline-none"
                    >
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </template>

            <!-- Input -->
            <input
                x-ref="input"
                x-model="query"
                @input="search"
                @keydown.enter.prevent="handleEnter"
                @keydown.backspace="handleBackspace"
                @keydown.delete="handleDelete"
                @keydown.tab="selectTag(suggestions[focusedIndex])"
                @keydown.down.prevent="handleArrowDown"
                @keydown.up.prevent="handleArrowUp"
                @focus="isFocused = true"
                @blur="handleBlur"
                @click="isFocused = true"
                type="text"
                class="inline-flex border-none focus:ring-0 text-sm flex-1 px-2 py-1 focus:outline-none min-w-[100px]"
                :placeholder="selected.length === 0 ? placeholder : ''"
            >
        </div>
    </div>

    <!-- Suggestions dropdown -->
    <div
        x-show="isFocused && (suggestions.length > 0 || query.length > 0)"
        @click.away="isFocused = false"
        class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border border-gray-300 py-1 text-base max-h-60 overflow-auto focus:outline-none sm:text-sm"
        x-cloak
    >
        <!-- List of suggestions -->
        <template x-for="(suggestion, index) in suggestions" :key="suggestion.id">
            <div
                @mouseenter="focusedIndex = index"
                @click="selectTag(suggestion)"
                :class="{ 'bg-blue-100': focusedIndex === index }"
                class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-blue-50"
            >
                <span x-text="suggestion.name" class="block truncate"></span>
            </div>
        </template>

        <!-- Create new tag option -->
        <div
            x-show="query.length > 0 && !suggestions.some(s => s.name.toLowerCase() === query.toLowerCase())"
            @click="createTag"
            class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-blue-600 hover:bg-blue-50"
        >
            <span class="block truncate">
                <span>Create "</span>
                <span x-text="query"></span>
                <span>"</span>
            </span>
        </div>
    </div>
</div> 