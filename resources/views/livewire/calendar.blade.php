<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 md:mb-0">
                        <i class="fas fa-calendar-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                        Calendar
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
                        
                        <button wire:click="currentMonth" class="inline-flex items-center justify-center px-4 py-2 ml-2 border border-gray-300 dark:border-gray-600 rounded-md font-medium text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
                            Today
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
                                                {{ $day['isToday'] ? 'ring-2 ring-purple-600 dark:ring-purple-500' : '' }}
                                                {{ $selectedDate && $selectedDate->format('Y-m-d') === $day['date'] ? 'bg-purple-50 dark:bg-purple-900' : '' }}
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
                                            
                                            @if(isset($monthTasks[$day['date']]) && count($monthTasks[$day['date']]) > 0)
                                                <div class="mt-1 space-y-1 max-h-28 overflow-hidden">
                                                    @foreach($monthTasks[$day['date']] as $task)
                                                        @if($loop->index < 2)
                                                            <div class="text-xs truncate p-1 rounded 
                                                                {{ $task->completed ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200' }}">
                                                                {{ $task->title }}
                                                            </div>
                                                        @elseif($loop->index == 2)
                                                            <div class="text-xs p-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                                                +{{ count($monthTasks[$day['date']]) - 2 }} more
                                                            </div>
                                                            @break
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected Day Tasks -->
                    <div class="lg:col-span-2">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 h-full">
                            @if($selectedDate)
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    {{ $selectedDate->format('F j, Y') }}
                                </h3>
                                
                                @if(count($selectedDateTasks) === 0)
                                    <div class="text-center py-8">
                                        <i class="fas fa-calendar-check text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No tasks scheduled for this day.</p>
                                        <div class="mt-4">
                                            <a href="{{ route('tasks.index') }}" class="inline-flex items-center text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300">
                                                <i class="fas fa-plus mr-1"></i> Add a task
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($selectedDateTasks as $task)
                                            <div class="py-3">
                                                <div class="flex items-start">
                                                    <button wire:click="toggleTaskStatus({{ $task->id }})" class="flex-shrink-0 mt-1 mr-2">
                                                        @if($task->completed)
                                                            <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-lg"></i>
                                                        @else
                                                            <i class="far fa-circle text-gray-400 dark:text-gray-500 text-lg"></i>
                                                        @endif
                                                    </button>
                                                    <div>
                                                        <a href="{{ route('tasks.show', $task->id) }}" class="block">
                                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white {{ $task->completed ? 'line-through text-gray-500 dark:text-gray-400' : '' }}">
                                                                {{ $task->title }}
                                                            </h4>
                                                            
                                                            @if($task->description)
                                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                                                    {{ $task->description }}
                                                                </p>
                                                            @endif
                                                            
                                                            <div class="mt-1 flex items-center space-x-2">
                                                                @if($task->category)
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                                                        {{ $task->category->name }}
                                                                    </span>
                                                                @endif
                                                                
                                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $task->due_date->format('g:i A') }}
                                                                </span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-calendar-day text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Select a date to view scheduled tasks.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 