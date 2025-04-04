@extends('layouts.app')

@section('title', 'Edit Smart Tag')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Smart Tag</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                Modify smart tag criteria and settings
            </p>
        </div>
        <a href="{{ route('smart-tags.index') }}" class="btn btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Smart Tags
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('smart-tags.update', $smartTag) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $smartTag->name) }}" required 
                            class="form-input w-full" placeholder="e.g., Important Tasks">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                        <div class="flex space-x-2">
                            <input type="color" name="color" id="color" value="{{ old('color', $smartTag->color) }}" 
                                class="h-10 w-10 rounded border border-gray-300 cursor-pointer">
                            <input type="text" name="color_hex" id="color_hex" value="{{ old('color', $smartTag->color) }}" 
                                class="form-input flex-grow" placeholder="#HEX">
                        </div>
                        @error('color')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Description -->
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                    <textarea name="description" id="description" rows="2" 
                        class="form-textarea w-full" placeholder="Brief description of this smart tag">{{ old('description', $smartTag->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Filters -->
            <div>
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Filter Criteria</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Tasks that match these criteria will automatically appear under this smart tag.
                </p>
                
                <!-- Due Date Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_due_date" id="filter_by_due_date" value="1" 
                                {{ old('filter_by_due_date', $smartTag->filter_by_due_date) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_due_date" class="font-medium text-gray-700 dark:text-gray-300">Filter by Due Date</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_due_date', $smartTag->filter_by_due_date) ? '' : 'hidden' }}" id="due_date_options">
                                <div class="space-y-4">
                                    <div>
                                        <select name="due_date_operator" id="due_date_operator" class="form-select w-full sm:w-auto">
                                            <option value="before" {{ old('due_date_operator', $smartTag->due_date_operator) == 'before' ? 'selected' : '' }}>Due before</option>
                                            <option value="after" {{ old('due_date_operator', $smartTag->due_date_operator) == 'after' ? 'selected' : '' }}>Due after</option>
                                            <option value="between" {{ old('due_date_operator', $smartTag->due_date_operator) == 'between' ? 'selected' : '' }}>Due between</option>
                                            <option value="overdue" {{ old('due_date_operator', $smartTag->due_date_operator) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                            <option value="today" {{ old('due_date_operator', $smartTag->due_date_operator) == 'today' ? 'selected' : '' }}>Due today</option>
                                            <option value="next_7_days" {{ old('due_date_operator', $smartTag->due_date_operator) == 'next_7_days' ? 'selected' : '' }}>Due in next 7 days</option>
                                        </select>
                                    </div>
                                    
                                    <div id="due_date_value_container" class="{{ in_array(old('due_date_operator', $smartTag->due_date_operator), ['overdue', 'today', 'next_7_days']) ? 'hidden' : '' }}">
                                        <div class="flex flex-wrap gap-4">
                                            <div>
                                                <input type="date" name="due_date_value" id="due_date_value" 
                                                    value="{{ old('due_date_value', $smartTag->due_date_value) }}" class="form-input">
                                            </div>
                                            
                                            <div id="due_date_end_container" class="{{ old('due_date_operator', $smartTag->due_date_operator) == 'between' ? '' : 'hidden' }}">
                                                <span class="mx-2">and</span>
                                                <input type="date" name="due_date_end_value" id="due_date_end_value" 
                                                    value="{{ old('due_date_end_value', $smartTag->due_date_end_value) }}" class="form-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Priority Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_priority" id="filter_by_priority" value="1" 
                                {{ old('filter_by_priority', $smartTag->filter_by_priority) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_priority" class="font-medium text-gray-700 dark:text-gray-300">Filter by Priority</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_priority', $smartTag->filter_by_priority) ? '' : 'hidden' }}" id="priority_options">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="priority_high" id="priority_high" value="1" 
                                            {{ old('priority_high', $smartTag->priority_high) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="priority_high" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            High Priority
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="priority_medium" id="priority_medium" value="1" 
                                            {{ old('priority_medium', $smartTag->priority_medium) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="priority_medium" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Medium Priority
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="priority_low" id="priority_low" value="1" 
                                            {{ old('priority_low', $smartTag->priority_low) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="priority_low" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Low Priority
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_status" id="filter_by_status" value="1" 
                                {{ old('filter_by_status', $smartTag->filter_by_status) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_status" class="font-medium text-gray-700 dark:text-gray-300">Filter by Status</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_status', $smartTag->filter_by_status) ? '' : 'hidden' }}" id="status_options">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="status_pending" id="status_pending" value="1"
                                            {{ old('status_pending', $smartTag->status_pending) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="status_pending" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Pending
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="status_in_progress" id="status_in_progress" value="1" 
                                            {{ old('status_in_progress', $smartTag->status_in_progress) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="status_in_progress" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            In Progress
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="status_completed" id="status_completed" value="1" 
                                            {{ old('status_completed', $smartTag->status_completed) ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                        <label for="status_completed" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Completed
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_category" id="filter_by_category" value="1" 
                                {{ old('filter_by_category', $smartTag->filter_by_category) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_category" class="font-medium text-gray-700 dark:text-gray-300">Filter by Category</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_category', $smartTag->filter_by_category) ? '' : 'hidden' }}" id="category_options">
                                <select name="category_ids[]" id="category_ids" multiple class="form-multiselect w-full" size="5">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('category_ids', $smartTag->category_ids)) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Hold Ctrl/Cmd to select multiple categories
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tags Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_tags" id="filter_by_tags" value="1" 
                                {{ old('filter_by_tags', $smartTag->filter_by_tags) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_tags" class="font-medium text-gray-700 dark:text-gray-300">Filter by Tags</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_tags', $smartTag->filter_by_tags) ? '' : 'hidden' }}" id="tags_options">
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <input type="radio" name="tags_filter_type" id="has_any_tags" value="has_any" 
                                                {{ old('tags_filter_type', $smartTag->tags_filter_type) === 'has_any' ? 'checked' : '' }}
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                            <label for="has_any_tags" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                                Has any tags
                                            </label>
                                        </div>
                                        <div class="flex items-center mb-2">
                                            <input type="radio" name="tags_filter_type" id="has_no_tags" value="has_none" 
                                                {{ old('tags_filter_type', $smartTag->tags_filter_type) === 'has_none' ? 'checked' : '' }}
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                            <label for="has_no_tags" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                                Has no tags
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="radio" name="tags_filter_type" id="has_specific_tags" value="has_specific" 
                                                {{ old('tags_filter_type', $smartTag->tags_filter_type) === 'has_specific' ? 'checked' : '' }}
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                            <label for="has_specific_tags" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                                Has specific tags
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div id="specific_tags_container" class="ml-6 {{ old('tags_filter_type', $smartTag->tags_filter_type) === 'has_specific' ? '' : 'hidden' }}">
                                        <select name="tag_ids[]" id="tag_ids" multiple class="form-multiselect w-full" size="5">
                                            @foreach($tags as $tag)
                                                <option value="{{ $tag->id }}" 
                                                    {{ in_array($tag->id, old('tag_ids', $smartTag->tag_ids)) ? 'selected' : '' }}>
                                                    {{ $tag->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Hold Ctrl/Cmd to select multiple tags
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Time Entries Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_time_entries" id="filter_by_time_entries" value="1" 
                                {{ old('filter_by_time_entries', $smartTag->filter_by_time_entries) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_time_entries" class="font-medium text-gray-700 dark:text-gray-300">Filter by Time Tracking</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_time_entries', $smartTag->filter_by_time_entries) ? '' : 'hidden' }}" id="time_entries_options">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="radio" name="has_time_entries" id="has_time_entries_yes" value="1" 
                                            {{ old('has_time_entries', $smartTag->has_time_entries) === '1' ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="has_time_entries_yes" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Has time entries
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="has_time_entries" id="has_time_entries_no" value="0" 
                                            {{ old('has_time_entries', $smartTag->has_time_entries) === '0' ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="has_time_entries_no" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Has no time entries
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Attachments Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_attachments" id="filter_by_attachments" value="1" 
                                {{ old('filter_by_attachments', $smartTag->filter_by_attachments) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_attachments" class="font-medium text-gray-700 dark:text-gray-300">Filter by Attachments</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_attachments', $smartTag->filter_by_attachments) ? '' : 'hidden' }}" id="attachments_options">
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input type="radio" name="has_attachments" id="has_attachments_yes" value="1" 
                                            {{ old('has_attachments', $smartTag->has_attachments) === '1' ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="has_attachments_yes" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Has attachments
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="has_attachments" id="has_attachments_no" value="0" 
                                            {{ old('has_attachments', $smartTag->has_attachments) === '0' ? 'checked' : '' }}
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                        <label for="has_attachments_no" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                            Has no attachments
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Creation Date Filter -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="filter_by_created_date" id="filter_by_created_date" value="1" 
                                {{ old('filter_by_created_date', $smartTag->filter_by_created_date) ? 'checked' : '' }}
                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="filter_by_created_date" class="font-medium text-gray-700 dark:text-gray-300">Filter by Creation Date</label>
                            
                            <div class="mt-2 ml-6 {{ old('filter_by_created_date', $smartTag->filter_by_created_date) ? '' : 'hidden' }}" id="created_date_options">
                                <div class="space-y-4">
                                    <div>
                                        <label for="created_date_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
                                        <input type="date" name="created_date_start" id="created_date_start" 
                                            value="{{ old('created_date_start', $smartTag->created_date_start) }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label for="created_date_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
                                        <input type="date" name="created_date_end" id="created_date_end" 
                                            value="{{ old('created_date_end', $smartTag->created_date_end) }}"
                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end pt-6 space-x-3">
                <button type="reset" class="btn btn-outline">Reset</button>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Update Smart Tag
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Script moved to resources/js/pages/smart-tags-form.js --}}
{{-- @push('scripts')
<script>
    // ... removed script content ...
</script>
@endpush --}}
@endsection 