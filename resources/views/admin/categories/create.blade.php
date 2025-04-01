<x-layouts.app>
    <x-slot:title>{{ __('Create Category') }}</x-slot:title>

    <div class="space-y-6">
        <x-header>
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold">{{ __('Create Category') }}</h1>
            </div>
        </x-header>
        
        <x-card>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="{{ __('Enter category name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Color') }}
                        </label>
                        <div class="flex mt-1">
                            <input type="color" name="color" id="color" value="{{ old('color', '#3b82f6') }}"
                                class="h-10 w-12 rounded-l-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                            <input type="text" name="color_hex" id="color_hex" value="{{ old('color', '#3b82f6') }}"
                                class="flex-1 rounded-r-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100"
                                readonly>
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Description') }}
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500"
                            placeholder="{{ __('Enter category description') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('Create Category') }}
                    </button>
                </div>
            </form>
        </x-card>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colorInput = document.getElementById('color');
            const colorHexInput = document.getElementById('color_hex');
            
            colorInput.addEventListener('input', function() {
                colorHexInput.value = this.value;
            });
        });
    </script>
</x-layouts.app> 