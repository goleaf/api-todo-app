<footer class="bg-white shadow mt-6">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
            </div>
            <div class="flex space-x-4 text-sm text-gray-500">
                <a href="#" class="hover:text-gray-700">{{ __('Terms') }}</a>
                <a href="#" class="hover:text-gray-700">{{ __('Privacy') }}</a>
                <a href="#" class="hover:text-gray-700">{{ __('Contact') }}</a>
            </div>
        </div>
    </div>
</footer> 