<footer class="bg-white border-t border-gray-200 mt-auto py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'Todo App') }}. {{ __('All rights reserved.') }}
            </div>
            <div class="flex items-center space-x-4">
                <a href="#" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Privacy Policy') }}</a>
                <a href="#" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Terms of Service') }}</a>
                <a href="#" class="text-sm text-gray-500 hover:text-gray-700">{{ __('Contact') }}</a>
            </div>
        </div>
    </div>
</footer> 