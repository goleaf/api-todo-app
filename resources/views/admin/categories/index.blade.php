<x-layouts.app>
    <x-slot:title>{{ __('Categories') }}</x-slot:title>

    <div class="space-y-6">
        <x-header>
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h1 class="text-2xl font-bold">{{ __('Categories') }}</h1>
                <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                    <x-phosphor-plus />
                    {{ __('New Category') }}
                </a>
            </div>
        </x-header>
        
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 dark:bg-red-900 dark:text-red-300" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        <x-card>
            @if($categories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-4 py-3">{{ __('Name') }}</th>
                                <th scope="col" class="px-4 py-3">{{ __('Color') }}</th>
                                <th scope="col" class="px-4 py-3">{{ __('Description') }}</th>
                                <th scope="col" class="px-4 py-3">{{ __('Tasks') }}</th>
                                <th scope="col" class="px-4 py-3">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-3 font-medium">
                                        <div class="flex items-center">
                                            <span class="inline-block size-4 rounded-full mr-2" style="background-color: {{ $category->color }}"></span>
                                            {{ $category->name }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <code class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ $category->color }}</code>
                                    </td>
                                    <td class="px-4 py-3 max-w-xs truncate">
                                        {{ $category->description ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.tasks.index', ['category' => $category->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $category->tasks_count }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-amber-600 dark:text-amber-400 hover:text-amber-800 dark:hover:text-amber-200">
                                                <x-phosphor-pencil-simple class="size-5" />
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200">
                                                    <x-phosphor-trash class="size-5" />
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="py-16 text-center text-gray-500 dark:text-gray-400">
                    <x-phosphor-folder class="mx-auto size-16 mb-4" />
                    <h3 class="text-lg font-medium mb-1">{{ __('No categories found') }}</h3>
                    <p class="mb-4">{{ __('Get started by creating your first category') }}</p>
                    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                        <x-phosphor-plus />
                        {{ __('New Category') }}
                    </a>
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app> 