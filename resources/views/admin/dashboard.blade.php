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
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Tasks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['active_users'] as $user)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user) }}">
                                            {{ $user->name }}
                                        </a>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->tasks_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No active users found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Task Status Chart
    const taskStatusCtx = document.getElementById('taskStatusChart').getContext('2d');
    const taskStatusChart = new Chart(taskStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'Incomplete', 'Overdue'],
            datasets: [{
                data: [
                    {{ $stats['completed_tasks_count'] }}, 
                    {{ $stats['incomplete_tasks_count'] - $stats['overdue_tasks_count'] }}, 
                    {{ $stats['overdue_tasks_count'] }}
                ],
                backgroundColor: [
                    '#28a745', // Success green
                    '#ffc107', // Warning yellow
                    '#dc3545'  // Danger red
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush 