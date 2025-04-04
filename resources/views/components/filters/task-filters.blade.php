@props(['categories', 'tags' => [], 'selectedCategory' => null, 'selectedTag' => null, 'selectedStatus' => null, 'selectedPriority' => null])

<div class="bg-white p-4 rounded-lg shadow mb-6 space-y-4">
    <h3 class="text-lg font-medium text-gray-900">{{ __('Filters') }}</h3>
    
    <form action="{{ request()->routeIs('tasks.*') ? route('tasks.index') : route('dashboard') }}" method="GET" class="space-y-4">
        <!-- Search -->
        <div>
            <x-components.ui.input-label for="search" :value="__('Search')" />
            <x-components.ui.text-input
                id="search"
                name="search"
                type="text"
                class="mt-1 block w-full"
                :value="request('search')"
                placeholder="{{ __('Search tasks...') }}"
            />
        </div>

        <!-- Category Filter -->
        <div>
            <x-components.ui.input-label for="category" :value="__('Category')" />
            <x-components.ui.select id="category" name="category" class="mt-1 block w-full">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </x-components.ui.select>
        </div>
        
        <!-- Tag Filter -->
        <div>
            <x-components.ui.input-label for="tag" :value="__('Tags')" />
            <x-components.ui.select id="tag" name="tag" class="mt-1 block w-full" onchange="this.form.submit()">
                <option value="">{{ __('All Tags') }}</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ $selectedTag && $selectedTag->id == $tag->id ? 'selected' : '' }}>
                        {{ $tag->name }}
                        @if($selectedTag && $selectedTag->id == $tag->id)
                        ({{ __('Selected') }})
                        @endif
                    </option>
                @endforeach
            </x-components.ui.select>
        </div>

        <!-- Status Filter -->
        <div>
            <x-components.ui.input-label for="status" :value="__('Status')" />
            <x-components.ui.select id="status" name="status" class="mt-1 block w-full">
                <option value="">{{ __('All Status') }}</option>
                <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="in_progress" {{ $selectedStatus === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
            </x-components.ui.select>
        </div>

        <!-- Priority Filter -->
        <div>
            <x-components.ui.input-label for="priority" :value="__('Priority')" />
            <x-components.ui.select id="priority" name="priority" class="mt-1 block w-full">
                <option value="">{{ __('All Priorities') }}</option>
                <option value="low" {{ $selectedPriority === 'low' ? 'selected' : '' }}>{{ __('Low') }}</option>
                <option value="medium" {{ $selectedPriority === 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                <option value="high" {{ $selectedPriority === 'high' ? 'selected' : '' }}>{{ __('High') }}</option>
            </x-components.ui.select>
        </div>

        <!-- Due Date Filter -->
        <div>
            <x-components.ui.input-label for="due_date" :value="__('Due Date')" />
            <x-components.ui.select id="due_date" name="due_date" class="mt-1 block w-full">
                <option value="">{{ __('All Dates') }}</option>
                <option value="overdue" {{ request('due_date') === 'overdue' ? 'selected' : '' }}>{{ __('Overdue') }}</option>
                <option value="today" {{ request('due_date') === 'today' ? 'selected' : '' }}>{{ __('Today') }}</option>
                <option value="tomorrow" {{ request('due_date') === 'tomorrow' ? 'selected' : '' }}>{{ __('Tomorrow') }}</option>
                <option value="this_week" {{ request('due_date') === 'this_week' ? 'selected' : '' }}>{{ __('This Week') }}</option>
                <option value="next_week" {{ request('due_date') === 'next_week' ? 'selected' : '' }}>{{ __('Next Week') }}</option>
            </x-components.ui.select>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <x-components.ui.secondary-button type="button" onclick="window.location.href='{{ request()->routeIs('tasks.*') ? route('tasks.index') : route('dashboard') }}'">
                {{ __('Clear') }}
            </x-components.ui.secondary-button>

            <x-components.ui.primary-button>
                {{ __('Apply Filters') }}
            </x-components.ui.primary-button>
        </div>
    </form>
</div> 