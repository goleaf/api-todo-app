@props(['categories' => [], 'tags' => [], 'selectedCategory' => null, 'selectedTag' => null, 'selectedStatus' => null, 'selectedPriority' => null])

<x-shared.components.ui.card>
    <div class="space-y-4">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Filters') }}</h3>
        
        <form action="{{ request()->routeIs('tasks.*') ? route('tasks.index') : route('dashboard') }}" method="GET" class="space-y-4">
            <!-- Search -->
            <x-shared.components.ui.input
                name="search"
                :label="__('Search')"
                :value="request('search')"
                :placeholder="__('Search tasks...')"
            />

            <!-- Category Filter -->
            @if(count($categories) > 0)
                @php
                    $categoryOptions = ['' => __('All Categories')];
                    foreach($categories as $category) {
                        $categoryOptions[$category->id] = $category->name;
                    }
                @endphp
                
                <x-shared.components.ui.select
                    name="category"
                    :label="__('Category')"
                    :options="$categoryOptions"
                    :selected="$selectedCategory"
                />
            @endif

            <!-- Tag Filter -->
            @if(count($tags) > 0)
                @php
                    $tagOptions = ['' => __('All Tags')];
                    foreach($tags as $tag) {
                        $tagOptions[$tag->id] = $tag->name;
                    }
                @endphp
                
                <x-shared.components.ui.select
                    name="tag"
                    :label="__('Tag')"
                    :options="$tagOptions"
                    :selected="$selectedTag"
                />
            @endif

            <!-- Status Filter -->
            <x-shared.components.ui.select
                name="status"
                :label="__('Status')"
                :options="[
                    '' => __('All Status'),
                    'pending' => __('Pending'),
                    'in_progress' => __('In Progress'),
                    'completed' => __('Completed')
                ]"
                :selected="$selectedStatus"
            />

            <!-- Priority Filter -->
            <x-shared.components.ui.select
                name="priority"
                :label="__('Priority')"
                :options="[
                    '' => __('All Priorities'),
                    'low' => __('Low'),
                    'medium' => __('Medium'),
                    'high' => __('High')
                ]"
                :selected="$selectedPriority"
            />

            <!-- Due Date Filter -->
            <x-shared.components.ui.select
                name="due_date"
                :label="__('Due Date')"
                :options="[
                    '' => __('All Dates'),
                    'overdue' => __('Overdue'),
                    'today' => __('Today'),
                    'tomorrow' => __('Tomorrow'),
                    'this_week' => __('This Week'),
                    'next_week' => __('Next Week')
                ]"
                :selected="request('due_date')"
            />

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-shared.components.ui.button
                    variant="secondary"
                    type="button"
                    :href="request()->routeIs('tasks.*') ? route('tasks.index') : route('dashboard')"
                >
                    {{ __('Clear') }}
                </x-shared.components.ui.button>

                <x-shared.components.ui.button
                    type="submit"
                >
                    {{ __('Apply Filters') }}
                </x-shared.components.ui.button>
            </div>
        </form>
    </div>
</x-shared.components.ui.card> 