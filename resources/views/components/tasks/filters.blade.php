@props(['categories', 'selectedStatus' => null, 'selectedCategory' => null, 'selectedPriority' => null])

<div class="bg-white shadow-sm rounded-lg p-4">
    <form action="{{ route('tasks.index') }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $selectedStatus === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" name="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select id="priority" name="priority" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Priorities</option>
                    <option value="low" {{ $selectedPriority === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ $selectedPriority === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ $selectedPriority === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ $selectedPriority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter
                </button>
            </div>
        </div>
    </form>
</div> 