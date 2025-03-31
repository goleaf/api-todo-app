@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Users</h6>
                        <h1 class="display-4">{{ $stats['users_count'] }}</h1>
                    </div>
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.users.index') }}" class="text-white">View Details</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Tasks</h6>
                        <h1 class="display-4">{{ $stats['tasks_count'] }}</h1>
                    </div>
                    <i class="fas fa-tasks fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.tasks.index') }}" class="text-white">View Details</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Categories</h6>
                        <h1 class="display-4">{{ $stats['categories_count'] }}</h1>
                    </div>
                    <i class="fas fa-folder fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.categories.index') }}" class="text-white">View Details</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase">Tags</h6>
                        <h1 class="display-4">{{ $stats['tags_count'] }}</h1>
                    </div>
                    <i class="fas fa-tag fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between">
                <a href="{{ route('admin.tags.index') }}" class="text-white">View Details</a>
                <i class="fas fa-angle-right text-white"></i>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end mb-3">
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-sm btn-outline-primary period-selector active" data-period="week">Last Week</button>
        <button type="button" class="btn btn-sm btn-outline-primary period-selector" data-period="month">Last Month</button>
        <button type="button" class="btn btn-sm btn-outline-primary period-selector" data-period="year">Last Year</button>
    </div>
</div>

<!-- Task Status Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Task Status</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <canvas id="taskStatusChart" width="100%" height="100"></canvas>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><span class="badge bg-success">Completed</span></td>
                                        <td>{{ $stats['completed_tasks_count'] }}</td>
                                        <td>
                                            @php
                                                $completionRate = $stats['tasks_count'] > 0 
                                                    ? round(($stats['completed_tasks_count'] / $stats['tasks_count']) * 100) 
                                                    : 0;
                                            @endphp
                                            {{ $completionRate }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-warning">Incomplete</span></td>
                                        <td>{{ $stats['incomplete_tasks_count'] }}</td>
                                        <td>
                                            @php
                                                $incompleteRate = $stats['tasks_count'] > 0 
                                                    ? round(($stats['incomplete_tasks_count'] / $stats['tasks_count']) * 100) 
                                                    : 0;
                                            @endphp
                                            {{ $incompleteRate }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-danger">Overdue</span></td>
                                        <td>{{ $stats['overdue_tasks_count'] }}</td>
                                        <td>
                                            @php
                                                $overdueRate = $stats['tasks_count'] > 0 
                                                    ? round(($stats['overdue_tasks_count'] / $stats['tasks_count']) * 100) 
                                                    : 0;
                                            @endphp
                                            {{ $overdueRate }}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge bg-primary">Due this week</span></td>
                                        <td colspan="2">{{ $stats['due_this_week'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Task Creation Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="taskTrendChart" width="100%" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Tasks by Priority</h5>
            </div>
            <div class="card-body">
                <canvas id="taskPriorityChart" width="100%" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">Top Categories</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>User</th>
                                <th>Tasks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['tasks_by_category'] as $category)
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: {{ $category->color ?? '#6c757d' }}">
                                            {{ $category->name }}
                                        </span>
                                    </td>
                                    <td>{{ $category->user->name ?? 'N/A' }}</td>
                                    <td>{{ $category->tasks_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No categories found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Top Users -->
<div class="row">
    <div class="col-md-7">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Recent Tasks</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['recent_tasks'] as $task)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.tasks.show', $task) }}">
                                            {{ Str::limit($task->title, 30) }}
                                        </a>
                                    </td>
                                    <td>{{ $task->user->name }}</td>
                                    <td>
                                        @if($task->completed)
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No tasks found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-sm btn-outline-primary">View All Tasks</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-5">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Most Active Users</h5>
            </div>
            <div class="card-body">
                <canvas id="userActivityChart" width="100%" height="160"></canvas>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All Users</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                    <a href="{{ route('admin.tasks.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Create New Task
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-info text-white">
                        <i class="fas fa-folder-plus"></i> Add New Category
                    </a>
                    <a href="{{ route('admin.tags.create') }}" class="btn btn-warning text-white">
                        <i class="fas fa-tag"></i> Add New Tag
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Charts configuration
    let charts = {};
    let currentPeriod = 'week';
    
    // Initial chart setup with placeholder data
    setupCharts();
    
    // Fetch data and update charts
    fetchChartData(currentPeriod);
    
    // Period selector event handlers
    document.querySelectorAll('.period-selector').forEach(button => {
        button.addEventListener('click', function() {
            if (!this.classList.contains('active')) {
                document.querySelectorAll('.period-selector').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                currentPeriod = this.dataset.period;
                fetchChartData(currentPeriod);
            }
        });
    });
    
    function setupCharts() {
        // Task Status Chart
        charts.taskStatus = new Chart(document.getElementById('taskStatusChart'), {
            type: 'doughnut',
            data: {
                labels: ['Completed', 'Pending', 'Overdue'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
        
        // Task Trend Chart
        charts.taskTrend = new Chart(document.getElementById('taskTrendChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Tasks Created',
                    data: [],
                    fill: false,
                    borderColor: '#0d6efd',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Task Priority Chart
        charts.taskPriority = new Chart(document.getElementById('taskPriorityChart'), {
            type: 'bar',
            data: {
                labels: ['Low', 'Medium', 'High', 'Urgent'],
                datasets: [{
                    label: 'Tasks by Priority',
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(255, 102, 0, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgb(40, 167, 69)',
                        'rgb(255, 193, 7)',
                        'rgb(255, 102, 0)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // User Activity Chart
        charts.userActivity = new Chart(document.getElementById('userActivityChart'), {
            type: 'horizontalBar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Tasks',
                    data: [],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    
    function fetchChartData(period) {
        fetch(`{{ route('admin.dashboard.chart-data') }}?period=${period}`)
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    updateCharts(response.data);
                }
            })
            .catch(error => console.error('Error fetching chart data:', error));
    }
    
    function updateCharts(data) {
        // Update Task Status Chart
        charts.taskStatus.data.datasets[0].data = [
            data.tasksByStatus.completed,
            data.tasksByStatus.pending,
            data.tasksByStatus.overdue
        ];
        charts.taskStatus.update();
        
        // Update Task Trend Chart
        charts.taskTrend.data.labels = data.tasksByDate.labels;
        charts.taskTrend.data.datasets[0].data = data.tasksByDate.data;
        charts.taskTrend.update();
        
        // Update Task Priority Chart
        charts.taskPriority.data.datasets[0].data = [
            data.tasksByPriority['0'] || 0, // Low
            data.tasksByPriority['1'] || 0, // Medium
            data.tasksByPriority['2'] || 0, // High
            data.tasksByPriority['3'] || 0  // Urgent
        ];
        charts.taskPriority.update();
        
        // Update User Activity Chart
        charts.userActivity.data.labels = data.mostActiveUsers.map(user => user.name);
        charts.userActivity.data.datasets[0].data = data.mostActiveUsers.map(user => user.tasks_count);
        charts.userActivity.update();
    }
});
</script>
@endpush 