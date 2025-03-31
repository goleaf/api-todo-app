<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button 
        @click="open = !open"
        type="button" 
        class="relative inline-flex items-center p-2 text-sm font-medium text-center text-gray-700 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-800"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        @if(count($notifications) > 0)
            <span class="absolute inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full -top-1 -right-1">
                {{ count($notifications) }}
            </span>
        @endif
    </button>

    <!-- Dropdown menu -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-md shadow-lg dark:bg-gray-800"
    >
        <div class="p-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <span class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</span>
            @if(count($notifications) > 0)
                <button 
                    wire:click="clearAllNotifications"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                >
                    Clear all
                </button>
            @endif
        </div>
        
        <div class="overflow-y-auto max-h-[350px]">
            @forelse($notifications as $index => $notification)
                <div class="flex px-4 py-3 border-b border-gray-200 dark:border-gray-700 relative hover:bg-gray-50 dark:hover:bg-gray-700">
                    <div class="mr-3 flex-shrink-0">
                        @if($notification['type'] === 'created')
                            <div class="p-2 text-white bg-green-500 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                        @elseif($notification['type'] === 'updated')
                            <div class="p-2 text-white bg-blue-500 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                        @elseif($notification['type'] === 'completed')
                            <div class="p-2 text-white bg-purple-500 rounded-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="w-full">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $notification['message'] }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($notification['time'])->diffForHumans() }}
                        </div>
                        
                        <!-- Task details preview -->
                        @if(isset($notification['task']))
                            <div class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-xs">
                                <div class="flex justify-between">
                                    <span class="font-semibold">Due:</span>
                                    <span>{{ \Carbon\Carbon::parse($notification['task']['due_date'])->format('M d, Y') }}</span>
                                </div>
                                @if(isset($notification['task']['category']))
                                    <div class="flex justify-between mt-1">
                                        <span class="font-semibold">Category:</span>
                                        <span class="px-2 py-0.5 rounded-full text-xs" 
                                            style="background-color: {{ $notification['task']['category']['color'] }}40; color: {{ $notification['task']['category']['color'] }};">
                                            {{ $notification['task']['category']['name'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <!-- Close button -->
                    <button 
                        wire:click="removeNotification({{ $index }})"
                        class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            @empty
                <div class="flex items-center justify-center h-24 text-gray-500 dark:text-gray-400">
                    <p>No notifications yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
