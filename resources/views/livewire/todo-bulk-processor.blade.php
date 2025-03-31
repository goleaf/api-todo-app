<div class="bg-white rounded-lg shadow-md p-6 my-6">
    <div class="mb-4">
        <h2 class="text-xl font-bold mb-2">Bulk Task Processor</h2>
        <p class="text-sm text-gray-600">Powered by Hypervel coroutines for improved performance</p>
    </div>

    @if($errorMessage)
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p>{{ $errorMessage }}</p>
    </div>
    @endif

    @if($isProcessing)
    <div class="mb-6">
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ ($processedCount / $totalToProcess) * 100 }}%"></div>
        </div>
        <p class="mt-2 text-sm text-gray-600">{{ $processingStatus }}</p>
        <p class="text-sm text-gray-600">Processed {{ $processedCount }} of {{ $totalToProcess }} tasks</p>
    </div>
    @endif

    <div class="mb-6">
        <div class="flex gap-2 mb-4">
            <button 
                wire:click="selectAll" 
                class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75"
            >
                Select All
            </button>
            <button 
                wire:click="deselectAll" 
                class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-75"
            >
                Deselect All
            </button>
            <div class="ml-auto">
                <span class="text-sm text-gray-600">{{ count($selectedTodos) }} selected</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="w-10 py-2 px-4 text-left">&nbsp;</th>
                        <th class="py-2 px-4 text-left">Title</th>
                        <th class="py-2 px-4 text-left">Status</th>
                        <th class="py-2 px-4 text-left">Priority</th>
                        <th class="py-2 px-4 text-left">Due Date</th>
                        <th class="py-2 px-4 text-left">Category</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->todos as $todo)
                    <tr class="border-t hover:bg-gray-50 {{ in_array($todo->id, $selectedTodos) ? 'bg-blue-50' : '' }}">
                        <td class="py-2 px-4">
                            <input 
                                type="checkbox" 
                                wire:click="toggleSelect({{ $todo->id }})" 
                                @checked(in_array($todo->id, $selectedTodos))
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            >
                        </td>
                        <td class="py-2 px-4">{{ $todo->title }}</td>
                        <td class="py-2 px-4">
                            @if($todo->completed)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Completed</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            @if($todo->priority === 'high')
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">High</span>
                            @elseif($todo->priority === 'medium')
                                <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">Medium</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Low</span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            @if($todo->due_date)
                                @if($todo->due_date->isPast() && !$todo->completed)
                                    <span class="text-red-600">{{ $todo->due_date->format('M d, Y') }}</span>
                                @elseif($todo->due_date->isToday())
                                    <span class="text-orange-600">Today</span>
                                @else
                                    <span>{{ $todo->due_date->format('M d, Y') }}</span>
                                @endif
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="py-2 px-4">
                            @if($todo->category)
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                    {{ ucfirst($todo->category) }}
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-500">No tasks found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-4">
        <div class="flex gap-4">
            <div class="w-1/3">
                <label for="operationType" class="block text-sm font-medium text-gray-700 mb-1">Operation</label>
                <select 
                    id="operationType"
                    wire:model.live="operationType" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    @if($isProcessing) disabled @endif
                >
                    @foreach($operations as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if($operationType === 'categorize')
            <div class="w-1/3">
                <label for="selectedCategory" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select 
                    id="selectedCategory"
                    wire:model.live="selectedCategory" 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    @if($isProcessing) disabled @endif
                >
                    <option value="">Select Category</option>
                    @foreach($categories as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    <div class="flex justify-end">
        <button 
            wire:click="processBulkOperation" 
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-75"
            @if($isProcessing) disabled @endif
        >
            Process Selected Tasks
        </button>
    </div>

    @if(!empty($results))
    <div class="mt-6">
        <h3 class="text-lg font-medium mb-2">Process Results</h3>
        <div class="bg-gray-50 p-4 rounded-md max-h-60 overflow-y-auto">
            <ul class="divide-y divide-gray-200">
                @foreach($results as $result)
                <li class="py-2 flex items-center gap-3">
                    @if($result['success'])
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    @endif
                    <span class="font-medium">{{ $result['title'] }}</span>
                    <span class="text-sm text-gray-600">{{ $result['message'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div> 