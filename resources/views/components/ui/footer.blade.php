<footer class="bg-white shadow mt-auto">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('terms') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Terms of Service
                </a>
                <a href="{{ route('privacy') }}" class="text-sm text-gray-500 hover:text-gray-700">
                    Privacy Policy
                </a>
            </div>
        </div>
    </div>
</footer> 