<x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
    {{ __('Dashboard') }}
</x-nav-link>

<x-nav-link :href="route('statistics')" :active="request()->routeIs('statistics')">
    {{ __('Statistics') }}
</x-nav-link> 