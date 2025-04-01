@extends('tablar::page')

@section('title', 'Dashboard')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Dashboard
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <!-- Task Statistics -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Tasks</div>
                            </div>
                            <div class="h1 mb-3">{{ $taskStats['total'] }}</div>
                            <div class="d-flex mb-2">
                                <div>All tasks in your account</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Completed Tasks</div>
                            </div>
                            <div class="h1 mb-3">{{ $taskStats['completed'] }}</div>
                            <div class="d-flex mb-2">
                                <div>Tasks you've completed</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Pending Tasks</div>
                            </div>
                            <div class="h1 mb-3">{{ $taskStats['pending'] }}</div>
                            <div class="d-flex mb-2">
                                <div>Tasks yet to be completed</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Overdue Tasks</div>
                            </div>
                            <div class="h1 mb-3 text-danger">{{ $taskStats['overdue'] }}</div>
                            <div class="d-flex mb-2">
                                <div>Tasks past their due date</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Statistics -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories</h3>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($categoryStats as $category)
                                    <div class="col-sm-6 col-lg-4">
                                        <div class="card card-sm">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <span class="bg-{{ Str::slug($category->name) }} text-white avatar">
                                                            {!! $category->icon ?? '📁' !!}
                                                        </span>
                                                    </div>
                                                    <div class="col">
                                                        <div class="font-weight-medium">
                                                            {{ $category->name }}
                                                        </div>
                                                        <div class="text-muted">
                                                            {{ $category->tasks_count }} pending tasks
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Tasks</h3>
                        </div>
                        <div class="card-table table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentTasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->category?->name ?? 'No Category' }}</td>
                                        <td>
                                            @if($task->completed)
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-primary">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Tasks -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Upcoming Tasks</h3>
                        </div>
                        <div class="card-table table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($upcomingTasks as $task)
                                    <tr>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->due_date?->format('Y-m-d') ?? 'No due date' }}</td>
                                        <td>
                                            @switch($task->priority)
                                                @case('high')
                                                    <span class="badge bg-danger">High</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge bg-warning">Medium</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-info">Low</span>
                                            @endswitch
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 