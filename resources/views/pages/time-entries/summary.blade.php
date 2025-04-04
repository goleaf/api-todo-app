@extends('layouts.app')

@section('title', 'Time Summary')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Time Summary</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-1">
                View your time tracking statistics and reports
            </p>
        </div>
        <a href="{{ route('time-entries.index') }}" class="btn btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Time Entries
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('time-entries.summary') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <div class="flex-1">
                <label for="date_range" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                <select name="date_range" id="date_range" class="form-select w-full">
                    <option value="this_week" {{ $dateRange === 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="last_week" {{ $dateRange === 'last_week' ? 'selected' : '' }}>Last Week</option>
                    <option value="this_month" {{ $dateRange === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $dateRange === 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="last_30_days" {{ $dateRange === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="last_90_days" {{ $dateRange === 'last_90_days' ? 'selected' : '' }}>Last 90 Days</option>
                    <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>

            <div id="custom-date-range" class="md:flex md:space-x-2 {{ $dateRange !== 'custom' ? 'hidden' : '' }}">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" 
                        value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                        class="form-input w-full">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" 
                        value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                        class="form-input w-full">
                </div>
            </div>
            
            <div>
                <label for="group_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Group By</label>
                <select name="group_by" id="group_by" class="form-select w-full">
                    <option value="day" {{ $groupBy === 'day' ? 'selected' : '' }}>Day</option>
                    <option value="week" {{ $groupBy === 'week' ? 'selected' : '' }}>Week</option>
                    <option value="month" {{ $groupBy === 'month' ? 'selected' : '' }}>Month</option>
                    <option value="category" {{ $groupBy === 'category' ? 'selected' : '' }}>Category</option>
                    <option value="task" {{ $groupBy === 'task' ? 'selected' : '' }}>Task</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary w-full md:w-auto mt-6 md:mt-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                    </svg>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Overall Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Total Time</h2>
            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalHours }}</div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Hours tracked in selected period</p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Daily Average</h2>
            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $dailyAverage }}</div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Average hours per day</p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Most Productive Day</h2>
            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $mostProductiveDay }}</div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $mostProductiveDayHours }} hours</p>
        </div>
    </div>
    
    {{-- Chart Container --}}
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <h3 class="text-lg font-semibold mb-4">{{ __('Time Distribution') }}</h3>
        <div class="h-64 md:h-80">
            <canvas id="timeDistributionChart" data-chart-data="{{ json_encode($chartData) }}"></canvas>
        </div>
    </div>
    
    <!-- Time Distribution Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Time Distribution by {{ ucfirst($groupBy) }}</h2>
        </div>
        
        @if($timeDistribution->isEmpty())
            <div class="p-6 text-center">
                <p class="text-gray-500 dark:text-gray-400">No time entries found for the selected period.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ ucfirst($groupBy) }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($timeDistribution as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($groupBy === 'category' && $item->color)
                                            <div class="h-3 w-3 rounded-full mr-2" style="background-color: {{ $item->color }}"></div>
                                        @endif
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $item->hours }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $item->percentage }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($item->percentage, 1) }}%</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    <!-- Top Tasks -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Top Tasks</h2>
        </div>
        
        @if($topTasks->isEmpty())
            <div class="p-6 text-center">
                <p class="text-gray-500 dark:text-gray-400">No tasks found for the selected period.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($topTasks as $task)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        <a href="{{ route('tasks.show', $task->id) }}" class="hover:underline">
                                            {{ $task->title }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($task->category)
                                            <div class="h-3 w-3 rounded-full mr-2" style="background-color: {{ $task->category->color }}"></div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $task->category->name }}
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Uncategorized
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white font-medium">{{ $task->hours }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'gray',
                                            'in_progress' => 'yellow',
                                            'completed' => 'green',
                                        ];
                                        $color = $statusColors[$task->status] ?? 'gray';
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-800 dark:text-{{ $color }}-100">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

{{-- Script moved to resources/js/pages/time-entries-summary.js --}}
{{-- @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> // Ensure Chart.js is installed via npm and imported in app.js
<script>
    // ... removed script content ...
</script>
@endpush --}} 