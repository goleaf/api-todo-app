@props(['categories', 'selectedStatus' => null, 'selectedCategory' => null, 'selectedPriority' => null])

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-4 sm:p-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <h3 class="text-lg font-medium text-gray-900 mb-4 sm:mb-0">{{ __('Filter Tasks') }}</h3>
            
            @if(request()->hasAny(['status', 'category', 'priority', 'tag', 'search']))
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Clear all filters') }}
                </a>
            @endif
        </div>
        
        <form action="{{ route('tasks.index') }}" method="GET" class="mt-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                    <select id="status" name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="in_progress" {{ $selectedStatus === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                        <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Category') }}</label>
                    <select id="category" name="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Priority Filter -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Priority') }}</label>
                    <select id="priority" name="priority" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">{{ __('All Priorities') }}</option>
                        <option value="low" {{ $selectedPriority === 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                        <option value="medium" {{ $selectedPriority === 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                        <option value="high" {{ $selectedPriority === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
                    </select>
                </div>
                
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search') }}</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="block w-full rounded-md border-gray-300 pr-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="{{ __('Search tasks...') }}">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ __('Apply Filters') }}
                </button>
            </div>
        </form>
    </div>
</div> 