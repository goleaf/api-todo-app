@extends('frontend.layouts.app')

@section('title', $task->title)

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">{{ $task->title }}</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">
                        <i class="ti ti-edit"></i>
                        Edit
                    </a>
                    <a href="{{ route('tasks.index') }}" class="btn">
                        <i class="ti ti-arrow-left"></i>
                        Back to Tasks
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="card-title">Task Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">Description</div>
                                <div class="datagrid-content">{{ $task->description ?: 'No description provided.' }}</div>
                            </div>
                            
                            <div class="datagrid-item">
                                <div class="datagrid-title">Category</div>
                                <div class="datagrid-content">
                                    @if($task->category)
                                        <a href="{{ route('categories.show', $task->category) }}" class="text-primary">{{ $task->category->name }}</a>
                                    @else
                                        <span class="text-muted">No category</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="datagrid-item">
                                <div class="datagrid-title">Due Date</div>
                                <div class="datagrid-content">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</div>
                            </div>
                            
                            <div class="datagrid-item">
                                <div class="datagrid-title">Status</div>
                                <div class="datagrid-content">
                                    @if($task->completed)
                                        <span class="status status-green">Completed</span>
                                    @else
                                        <span class="status status-yellow">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Time Entries</h3>
                        <div class="card-actions">
                            <a href="{{ route('time-entries.create', ['task_id' => $task->id]) }}" class="btn btn-primary">
                                <i class="ti ti-plus"></i>
                                Add Time Entry
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($task->timeEntries->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Duration</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($task->timeEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->start_time->format('M d, Y H:i') }}</td>
                                                <td>{{ $entry->end_time ? $entry->end_time->format('M d, Y H:i') : 'In Progress' }}</td>
                                                <td>
                                                    @if($entry->end_time)
                                                        {{ $entry->duration }} minutes
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-list flex-nowrap">
                                                        <a href="{{ route('time-entries.edit', $entry) }}" class="btn btn-icon btn-ghost-primary" title="Edit">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        <form action="{{ route('time-entries.destroy', $entry) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-icon btn-ghost-danger" onclick="return confirm('Are you sure you want to delete this time entry?')" title="Delete">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="ti ti-clock"></i>
                                </div>
                                <p class="empty-title">No time entries</p>
                                <p class="empty-subtitle text-muted">
                                    No time entries have been recorded for this task yet.
                                </p>
                                <div class="empty-action">
                                    <a href="{{ route('time-entries.create', ['task_id' => $task->id]) }}" class="btn btn-primary">
                                        <i class="ti ti-plus"></i>
                                        Add Time Entry
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tags</h3>
                    </div>
                    <div class="card-body">
                        @forelse($task->tags as $tag)
                            <a href="{{ route('tags.show', $tag) }}" class="badge bg-{{ $tag->color ?? 'blue' }}-lt me-1">
                                {{ $tag->name }}
                            </a>
                        @empty
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="ti ti-tags"></i>
                                </div>
                                <p class="empty-title">No tags</p>
                                <p class="empty-subtitle text-muted">
                                    No tags have been assigned to this task yet.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 