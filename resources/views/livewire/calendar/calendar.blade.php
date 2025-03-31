<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 md:mb-0">
                        <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                        Task Calendar
                    </h1>
                    
                    <!-- Month Navigation -->
                    <div class="flex items-center space-x-4">
                        <button wire:click="previousMonth" class="inline-flex items-center justify-center p-2 border border-transparent rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ $this->monthName }}</h2>
                        
                        <button wire:click="nextMonth" class="inline-flex items-center justify-center p-2 border border-transparent rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        
                        <button wire:click="jumpToToday" class="inline-flex items-center justify-center px-4 py-2 ml-2 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                            Today
                        </button>
                        
                        <button wire:click="toggleShowCompleted" class="inline-flex items-center justify-center px-4 py-2 ml-2 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                            {{ $showCompleted ? 'Hide Completed' : 'Show Completed' }}
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
                    <!-- Calendar -->
                    <div class="lg:col-span-5">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden">
                            <!-- Day names -->
                            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700 text-center">
                                <div class="py-2 font-semibold text-xs text-gray-700 dark:text-gray-300">Sun</div>
                                <div class="py-2 font-semibold text-xs text-gray-700 dark:text-gray-300">Mon</div>
                                <div class="py-2 font-semibold text-xs text-gray-700 dark:text-gray-300">Tue</div>
                                <div class="py-2 font-semibold text-xs text-gray-700 dark:text-gray-300">Wed</div>
                                <div class="py-2 font-semibold text-xs text-gray-700 dark:text-gray-300">Thu</div>
                                <div class="py-2 font-semibold text-xs text-gray-700 dark:text-gray-300">Fri</div>
                                <div class="py-2 font-semibold text-xs text-gray-700 dark:text-gray-300">Sat</div>
                            </div>
                            
                            <!-- Calendar grid -->
                            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                                @foreach($weeks as $week)
                                    @foreach($week as $day)
                                        <div 
                                            wire:click="selectDate('{{ $day['date'] }}')"
                                            class="min-h-24 p-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white 
                                                {{ !$day['isCurrentMonth'] ? 'bg-gray-50 dark:bg-gray-900 text-gray-400 dark:text-gray-500' : '' }}
                                                {{ $day['isToday'] ? 'calendar-today ring-2 ring-purple-600 dark:ring-purple-500' : '' }}
                                                {{ $selectedDate === $day['date'] ? 'bg-purple-50 dark:bg-purple-900' : '' }}
                                                cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
                                        >
                                            <div class="flex justify-between items-center">
                                                <span class="{{ $day['isWeekend'] && $day['isCurrentMonth'] ? 'text-red-500 dark:text-red-400' : '' }} {{ $day['isToday'] ? 'font-bold' : '' }}">
                                                    {{ $day['day'] }}
                                                </span>
                                                
                                                @if(isset($monthTasks[$day['date']]) && count($monthTasks[$day['date']]) > 0)
                                                    <span class="flex items-center justify-center h-5 w-5 text-xs font-semibold bg-purple-600 dark:bg-purple-500 text-white rounded-full">
                                                        {{ count($monthTasks[$day['date']]) }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Preview of tasks for this day (limited to 2) -->
                                            @if(isset($monthTasks[$day['date']]) && count($monthTasks[$day['date']]) > 0)
                                                <div class="mt-1 space-y-1">
                                                    @foreach(array_slice($monthTasks[$day['date']], 0, 2) as $task)
                                                        <div class="text-xs truncate {{ $task->completed ? 'line-through text-gray-400 dark:text-gray-500' : '' }}">
                                                            {{ $task->title }}
                                                        </div>
                                                    @endforeach
                                                    
                                                    @if(count($monthTasks[$day['date']]) > 2)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            + {{ count($monthTasks[$day['date']]) - 2 }} more...
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Task details for selected date -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow-sm">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ count($selectedDateTasks) }} tasks for this day
                                </p>
                            </div>
                            
                            <div class="p-4">
                                @if(count($selectedDateTasks) > 0)
                                    <ul class="space-y-3">
                                        @foreach($selectedDateTasks as $task)
                                            <li class="flex items-start space-x-3 p-3 rounded-lg {{ $task->completed ? 'bg-gray-50 dark:bg-gray-700' : 'bg-white dark:bg-gray-800' }}">
                                                <div class="flex-shrink-0">
                                                    <button wire:click="toggleTaskStatus({{ $task->id }})" class="flex items-center justify-center h-6 w-6 rounded-md border {{ $task->completed ? 'border-green-500 bg-green-100 dark:bg-green-900 dark:border-green-600' : 'border-gray-300 dark:border-gray-600' }}">
                                                        @if($task->completed)
                                                            <i class="fas fa-check text-green-600 dark:text-green-400 text-xs"></i>
                                                        @endif
                                                    </button>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white {{ $task->completed ? 'line-through text-gray-500 dark:text-gray-400' : '' }}">
                                                        {{ $task->title }}
                                                    </p>
                                                    @if($task->description)
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 {{ $task->completed ? 'line-through' : '' }}">
                                                            {{ $task->description }}
                                                        </p>
                                                    @endif
                                                    
                                                    @if($task->completed && $task->completed_at)
                                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                            Completed {{ $task->completed_at->diffForHumans() }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-8">
                                        <p class="text-gray-500 dark:text-gray-400">
                                            No tasks for this day
                                        </p>
                                        <a href="{{ route('tasks.create', ['due_date' => $selectedDate]) }}" class="inline-flex items-center mt-4 px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add Task
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 