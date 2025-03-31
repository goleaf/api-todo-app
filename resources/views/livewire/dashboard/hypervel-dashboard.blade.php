<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
            Hypervel Dashboard Demo
        </h2>
        <div class="flex items-center space-x-2">
            <button wire:click="refreshDashboard" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
            <button wire:click="toggleComparison" class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                {{ $showComparison ? 'Hide Comparison' : 'Compare Performance' }}
            </button>
        </div>
    </div>
    
    @if ($errorMessage)
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ $errorMessage }}</p>
        </div>
    @endif
    
    @if($isLoading)
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>
    @else
        <div class="mb-4">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <div class="mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-blue-700 dark:text-blue-300">Hypervel Load Time</p>
                        <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">{{ $loadTime }} ms</p>
                    </div>
                </div>
                
                @if($showComparison && $comparisonTime > 0)
                    <div class="mt-4 pt-4 border-t border-blue-100 dark:border-blue-800">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Sequential Load Time</p>
                                <p class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $comparisonTime }} ms</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Performance Improvement</p>
                                <p class="text-xl font-semibold {{ $this->getImprovementPercentage() >= 50 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' }}">
                                    {{ $this->getImprovementPercentage() }}% faster
                                </p>
                            </div>
                        </div>
                        
                        <div class="mt-3 bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden">
                            <div class="bg-green-500 h-4 rounded-full" style="width: {{ $this->getImprovementPercentage() }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Task Stats -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Task Stats</h3>
                <div class="grid grid-cols-2 gap-4">
                    @if(isset($dashboardData['taskStats']))
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Tasks</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $dashboardData['taskStats']['total'] }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Completed</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $dashboardData['taskStats']['completed'] }}</p>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $dashboardData['taskStats']['pending'] }}</p>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/20 p-3 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Overdue</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $dashboardData['taskStats']['overdue'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Recent Tasks -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Tasks</h3>
                @if(isset($dashboardData['tasks']) && count($dashboardData['tasks']) > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($dashboardData['tasks'] as $task)
                            <li class="py-2">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-block h-3 w-3 rounded-full {{ $task->status === 'completed' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $task->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No recent tasks found.</p>
                @endif
            </div>
            
            <!-- Upcoming Tasks -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Upcoming Tasks</h3>
                @if(isset($dashboardData['upcomingTasks']) && count($dashboardData['upcomingTasks']) > 0)
                    <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($dashboardData['upcomingTasks'] as $task)
                            <li class="py-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="inline-block h-3 w-3 rounded-full bg-blue-500"></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</p>
                                        </div>
                                    </div>
                                    <div class="text-xs text-blue-600 dark:text-blue-400">
                                        {{ $task->due_date ? $task->due_date->format('M d') : 'No date' }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No upcoming tasks found.</p>
                @endif
            </div>
            
            <!-- Categories -->
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Popular Categories</h3>
                @if(isset($dashboardData['popularCategories']) && count($dashboardData['popularCategories']) > 0)
                    <ul class="space-y-3">
                        @foreach($dashboardData['popularCategories'] as $category)
                            <li>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $category['name'] }}</span>
                                    <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300 rounded-full">{{ $category['count'] }} tasks</span>
                                </div>
                                <div class="mt-1 w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, $category['count'] * 5) }}%"></div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No categories found.</p>
                @endif
            </div>
        </div>
    @endif
    
    <div class="mt-6 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-4">
        <p>This dashboard demonstrates how the Hypervel service can be used to load multiple data sets concurrently, providing significant performance improvements over sequential loading.</p>
        <p class="mt-2">Click "Compare Performance" to see the difference between concurrent and sequential loading.</p>
    </div>
</div> 