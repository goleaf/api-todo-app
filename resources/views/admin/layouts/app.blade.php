<x-layouts.app.sidebar :title="$title ?? 'Admin'">
    <x-slot name="headerLeft">
        <x-navlist class="p-0 [&>ul]:space-y-0">
            <div>
                <x-navlist.item before="phosphor-graph" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-navlist.item>
                <x-navlist.item before="phosphor-list-checks" :href="route('admin.tasks.index')" :current="request()->routeIs('admin.tasks.*')">
                    {{ __('Tasks') }}
                </x-navlist.item>
                <x-navlist.item before="phosphor-tag" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')">
                    {{ __('Categories') }}
                </x-navlist.item>
            </div>
        </x-navlist>
    </x-slot>
    
    <x-container class="max-w-8xl py-6">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 dark:bg-green-900 dark:border-green-600 dark:text-green-300" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif
        
        {{ $slot }}
    </x-container>
</x-layouts.app.sidebar> 