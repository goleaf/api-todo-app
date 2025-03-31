@extends('admin.layouts.app')

@section('title', 'Task Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
    <li class="breadcrumb-item active">Task Details</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Task Information</h5>
                <div>
                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h2 class="d-flex align-items-center">
                        {{ $task->title }}
                        <form action="{{ route('admin.tasks.toggle', $task) }}" method="POST" class="ms-3">
                            @csrf
                            <button type="submit" class="btn {{ $task->completed ? 'btn-success' : 'btn-warning' }}">
                                {{ $task->completed ? 'Completed' : 'Pending' }}
                            </button>
                        </form>
                    </h2>
                    <p class="text-muted mb-1">Created {{ $task->created_at->diffForHumans() }} by <strong>{{ $task->user->name }}</strong></p>
                    <p class="text-muted">Last updated {{ $task->updated_at->diffForHumans() }}</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Priority</h6>
                        <span class="badge bg-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                    </div>
                    <div class="col-md-6">
                        <h6>Due Date</h6>
                        @if($task->due_date)
                            <span class="{{ $task->isOverdue() ? 'text-danger' : '' }}">
                                {{ $task->due_date->format('F d, Y') }}
                                @if($task->isOverdue())
                                    <span class="badge bg-danger">Overdue</span>
                                @endif
                            </span>
                        @else
                            <span class="text-muted">No due date</span>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <h6>Progress</h6>
                    <div class="progress" style="height: 24px;">
                        <div class="progress-bar {{ $task->progress == 100 ? 'bg-success' : 'bg-info' }}" 
                             role="progressbar" 
                             style="width: {{ $task->progress }}%;" 
                             aria-valuenow="{{ $task->progress }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $task->progress }}%
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6>Category</h6>
                    @if($task->category)
                        <span class="badge" style="background-color: {{ $task->category->color }}">
                            {{ $task->category->name }}
                        </span>
                    @else
                        <span class="text-muted">No category</span>
                    @endif
                </div>

                <div class="mb-4">
                    <h6>Description</h6>
                    <div class="card">
                        <div class="card-body bg-light">
                            {!! nl2br(e($task->description)) ?: '<em class="text-muted">No description provided</em>' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Tags -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tags</h5>
            </div>
            <div class="card-body">
                @if(isset($task->tags) && (is_object($task->tags) ? $task->tags->count() > 0 : !empty($task->tags)))
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(is_object($task->tags) ? $task->tags : collect($task->tags) as $tag)
                            <span class="badge" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No tags assigned</p>
                @endif
            </div>
        </div>

        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Activity Timeline</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Created</div>
                            By {{ $task->user->name }}
                        </div>
                        <span class="text-muted">{{ $task->created_at->format('M d, Y H:i') }}</span>
                    </li>
                    @if($task->updated_at->gt($task->created_at))
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Updated</div>
                        </div>
                        <span class="text-muted">{{ $task->updated_at->format('M d, Y H:i') }}</span>
                    </li>
                    @endif
                    @if($task->completed)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Completed</div>
                        </div>
                        <span class="text-muted">{{ $task->updated_at->format('M d, Y H:i') }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">Back to Tasks</a>
</div>
@endsection 