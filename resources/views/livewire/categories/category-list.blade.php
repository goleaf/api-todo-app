<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Categories</h1>
    
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <!-- Search and Create Button -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Search categories..."
                class="border rounded px-3 py-2 text-sm mr-2"
            >
        </div>
        
        <a href="{{ route('categories.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Create New Category
        </a>
    </div>
    
    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                        Name
                        @if ($sortField === 'name')
                            @if ($sortDirection === 'asc')
                                <span class="ml-1">↑</span>
                            @else
                                <span class="ml-1">↓</span>
                            @endif
                        @endif
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Color
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tasks
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if ($category->icon)
                                    <span class="mr-2">
                                        <i class="fas fa-{{ $category->icon }}"></i>
                                    </span>
                                @endif
                                <span class="font-medium">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div 
                                    class="w-6 h-6 rounded-full mr-2" 
                                    style="background-color: {{ $category->color }};"
                                ></div>
                                <span>{{ $category->color }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 truncate max-w-xs">
                                {{ $category->description ?: 'No description' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $category->tasks_count ?? $category->tasks()->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a 
                                href="{{ route('categories.edit', $category) }}" 
                                class="text-blue-600 hover:text-blue-900 mr-3"
                            >
                                Edit
                            </a>
                            <button 
                                wire:click="confirmDelete({{ $category->id }})"
                                class="text-red-600 hover:text-red-900"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No categories found. Create your first category!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $categories->links() }}
    </div>
    
    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-medium mb-4">
                    Delete Category
                </h3>
                
                <p class="mb-4">
                    Are you sure you want to delete this category?
                    @if ($this->taskCount > 0)
                        <span class="block text-red-600 font-bold mt-2">
                            Warning: This category has {{ $this->taskCount }} tasks. Deleting it will affect these tasks.
                        </span>
                    @endif
                </p>
                
                <div class="flex justify-end gap-2">
                    <button 
                        wire:click="cancelDelete" 
                        class="px-4 py-2 border rounded-md"
                    >
                        Cancel
                    </button>
                    <button 
                        wire:click="deleteCategory" 
                        class="px-4 py-2 bg-red-600 text-white rounded-md"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
