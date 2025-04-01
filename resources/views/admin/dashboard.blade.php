@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Admin Dashboard
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <!-- Stats Overview -->
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">Total Tasks</div>
                            </div>
                            <div class="h1 mb-3">{{ $totalTasks ?? 0 }}</div>
                            <div class="d-flex mb-2">
                                <div>All tasks in the system</div>
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
                            <div class="h1 mb-3">{{ $completedTasks ?? 0 }}</div>
                            <div class="d-flex mb-2">
                                <div>Tasks marked as completed</div>
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
                            <div class="h1 mb-3">{{ $pendingTasks ?? 0 }}</div>
                            <div class="d-flex mb-2">
                                <div>Tasks still in progress</div>
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
                            <div class="h1 mb-3 text-danger">{{ $overdueTasks ?? 0 }}</div>
                            <div class="d-flex mb-2">
                                <div>Tasks past their due date</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Recent Tasks</h3>
                            <div class="card-actions">
                                <a href="{{ route('tasks.index') }}" class="btn btn-primary">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-table table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentTasks ?? [] as $task)
                                        <tr>
                                            <td>
                                                <a href="{{ route('tasks.show', $task) }}" class="text-reset">
                                                    {{ $task->title }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($task->due_date)
                                                    {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                                @else
                                                    No due date
                                                @endif
                                            </td>
                                            <td>
                                                @if($task->status === 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-sm btn-primary">
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No tasks found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Time Tracking Overview -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Time Tracking</h3>
                            <div class="card-actions">
                                <a href="{{ route('time-entries.index') }}" class="btn btn-primary">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-muted mb-2">This Week</h4>
                                <div class="h1 mb-0">
                                    {{ $weeklyTime ?? '00:00' }}
                                    <span class="text-sm font-normal text-muted">hours</span>
                                </div>
                            </div>
                            
                            <div class="card-table table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Task</th>
                                            <th>Time</th>
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentTimeEntries ?? [] as $timeEntry)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('tasks.show', $timeEntry->task) }}" class="text-reset">
                                                        {{ $timeEntry->task->title }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($timeEntry->start_time)->format('M d, Y H:i') }}
                                                    @if($timeEntry->end_time)
                                                        - {{ \Carbon\Carbon::parse($timeEntry->end_time)->format('H:i') }}
                                                    @else
                                                        <span class="badge bg-success">Running</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($timeEntry->end_time)
                                                        {{ \Carbon\Carbon::parse($timeEntry->start_time)->diff(\Carbon\Carbon::parse($timeEntry->end_time))->format('%H:%I') }}
                                                    @else
                                                        --:--
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No time entries found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
