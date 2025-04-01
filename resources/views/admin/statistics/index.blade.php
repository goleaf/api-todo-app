@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Statistics & Analytics</h1>
        <p class="text-gray-600 mt-2">Track your productivity and task management metrics</p>
    </div>

    <!-- Period Filter -->
    <div class="mb-8 bg-white rounded-lg shadow p-4">
        <form id="period-form" class="flex items-center space-x-4">
            <label for="period" class="font-medium text-gray-700">Time Period:</label>
            <select id="period" name="period" class="form-select rounded-md border-gray-300">
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
                <option value="all">All Time</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Update
            </button>
        </form>
    </div>

    <!-- Task Statistics -->
    <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Overview</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Tasks</span>
                    <span class="text-2xl font-bold" id="total-tasks">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Completed</span>
                    <span class="text-2xl font-bold text-green-600" id="completed-tasks">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">In Progress</span>
                    <span class="text-2xl font-bold text-blue-600" id="in-progress-tasks">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Todo</span>
                    <span class="text-2xl font-bold text-gray-600" id="todo-tasks">0</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Status</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Completion Rate</span>
                    <span class="text-2xl font-bold text-green-600" id="completion-rate">0%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Overdue Tasks</span>
                    <span class="text-2xl font-bold text-red-600" id="overdue-tasks">0</span>
                </div>
            </div>
            <div class="mt-4" id="completion-chart"></div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Priority Distribution</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">High Priority</span>
                    <span class="text-2xl font-bold text-red-600" id="high-priority">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Medium Priority</span>
                    <span class="text-2xl font-bold text-yellow-600" id="medium-priority">0</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Low Priority</span>
                    <span class="text-2xl font-bold text-green-600" id="low-priority">0</span>
                </div>
            </div>
            <div class="mt-4" id="priority-chart"></div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Distribution</h3>
            <div id="category-list" class="space-y-2 max-h-64 overflow-y-auto">
                <!-- Categories will be populated via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Time Tracking Statistics -->
    <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Time Overview</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Time</span>
                    <span class="text-2xl font-bold" id="total-time">0h</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Average Daily Time</span>
                    <span class="text-2xl font-bold" id="avg-daily-time">0h</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Most Productive Day</span>
                    <span class="text-2xl font-bold" id="most-productive-day">-</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Hourly Distribution</h3>
            <div id="hourly-chart" class="h-64"></div>
        </div>
    </div>

    <!-- Productivity Trends -->
    <div class="mb-8 bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Productivity Trends</h3>
            <div class="flex items-center space-x-4">
                <label for="trend-days" class="text-gray-600">Days:</label>
                <select id="trend-days" class="form-select rounded-md border-gray-300">
                    <option value="7">7 Days</option>
                    <option value="30" selected>30 Days</option>
                    <option value="90">90 Days</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4">
            <div id="completion-trend-chart" class="h-64"></div>
            <div id="time-trend-chart" class="h-64"></div>
            <div id="completion-time-chart" class="h-64"></div>
        </div>
    </div>

    <!-- Tag Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Most Used Tags</h3>
            <div id="tag-list" class="space-y-2">
                <!-- Tags will be populated via JavaScript -->
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tag Completion Rates</h3>
            <div id="tag-completion-chart" class="h-64"></div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    let charts = {
        completion: null,
        priority: null,
        hourly: null,
        completionTrend: null,
        timeTrend: null,
        completionTime: null,
        tagCompletion: null
    };

    // Load initial data
    loadStatistics();

    // Event listeners
    document.getElementById('period-form').addEventListener('submit', function(e) {
        e.preventDefault();
        loadStatistics();
    });

    document.getElementById('trend-days').addEventListener('change', function() {
        loadProductivityTrends();
    });

    function loadStatistics() {
        const period = document.getElementById('period').value;
        
        // Load task statistics
        fetch(`/api/statistics/tasks?period=${period}`)
            .then(response => response.json())
            .then(data => updateTaskStats(data.data));

        // Load time statistics
        fetch(`/api/statistics/time?period=${period}`)
            .then(response => response.json())
            .then(data => updateTimeStats(data.data));

        // Load tag statistics
        fetch(`/api/statistics/tags?period=${period}`)
            .then(response => response.json())
            .then(data => updateTagStats(data.data));

        // Load productivity trends
        loadProductivityTrends();
    }

    function loadProductivityTrends() {
        const days = document.getElementById('trend-days').value;
        fetch(`/api/statistics/productivity?days=${days}`)
            .then(response => response.json())
            .then(data => updateProductivityTrends(data.data));
    }

    function updateTaskStats(data) {
        // Update task counters
        document.getElementById('total-tasks').textContent = data.total;
        document.getElementById('completed-tasks').textContent = data.completed;
        document.getElementById('in-progress-tasks').textContent = data.in_progress;
        document.getElementById('todo-tasks').textContent = data.todo;
        document.getElementById('completion-rate').textContent = `${data.completion_rate}%`;
        document.getElementById('overdue-tasks').textContent = data.overdue;
        document.getElementById('high-priority').textContent = data.priority_distribution.high;
        document.getElementById('medium-priority').textContent = data.priority_distribution.medium;
        document.getElementById('low-priority').textContent = data.priority_distribution.low;

        // Update category list
        const categoryList = document.getElementById('category-list');
        categoryList.innerHTML = data.category_distribution
            .map(category => `
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">${category.name}</span>
                    <span class="font-semibold">${category.tasks_count}</span>
                </div>
            `).join('');

        // Update charts
        updateCompletionChart(data);
        updatePriorityChart(data.priority_distribution);
    }

    function updateTimeStats(data) {
        // Update time counters
        document.getElementById('total-time').textContent = `${Math.round(data.total_time)}h`;
        document.getElementById('avg-daily-time').textContent = `${data.average_daily_time}h`;
        if (data.most_tracked_day) {
            document.getElementById('most-productive-day').textContent = 
                `${moment(data.most_tracked_day.date).format('MMM D')} (${Math.round(data.most_tracked_day.total_duration)}h)`;
        }

        // Update hourly distribution chart
        updateHourlyChart(data.hourly_distribution);
    }

    function updateTagStats(data) {
        // Update tag list
        const tagList = document.getElementById('tag-list');
        tagList.innerHTML = data.most_used_tags
            .map(tag => `
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">${tag.name}</span>
                    <span class="font-semibold">${tag.tasks_count}</span>
                </div>
            `).join('');

        // Update tag completion chart
        updateTagCompletionChart(data.tag_completion_rates);
    }

    function updateProductivityTrends(data) {
        updateCompletionTrendChart(data.task_completion_trend);
        updateTimeTrendChart(data.time_tracking_trend);
        updateCompletionTimeChart(data.completion_time_trend);
    }

    // Chart update functions
    function updateCompletionChart(data) {
        const ctx = document.getElementById('completion-chart').getContext('2d');
        if (charts.completion) charts.completion.destroy();
        
        charts.completion = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'In Progress', 'Todo'],
                datasets: [{
                    data: [data.completed, data.in_progress, data.todo],
                    backgroundColor: ['#10B981', '#3B82F6', '#6B7280']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // ... Similar chart update functions for other charts ...
});
</script>
@endpush 