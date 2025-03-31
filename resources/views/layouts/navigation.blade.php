<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>
    
    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')">
        {{ __('Tasks') }}
    </x-nav-link>
    
    <x-nav-link :href="route('calendar')" :active="request()->routeIs('calendar')">
        {{ __('Calendar') }}
    </x-nav-link>
    
    <x-nav-link :href="route('stats')" :active="request()->routeIs('stats')">
        {{ __('Stats') }}
    </x-nav-link>
    
    <x-nav-link :href="route('hypervel.demo')" :active="request()->routeIs('hypervel.demo')">
        {{ __('Hypervel Demo') }}
    </x-nav-link>
    
    <x-nav-link :href="route('todomvc')" :active="request()->routeIs('todomvc')">
        {{ __('TaskMVC') }}
    </x-nav-link>
</div> 