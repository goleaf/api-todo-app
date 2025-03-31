@extends('admin.layouts.app')

@section('title', 'Tag Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
    <li class="breadcrumb-item active">{{ $tag->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                Tag Information
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $tag->id }}
                </div>
                <div class="mb-3">
                    <strong>Name:</strong> {{ $tag->name }}
                </div>
                <div class="mb-3">
                    <strong>User:</strong> {{ $tag->user->name }} ({{ $tag->user->email }})
                </div>
                <div class="mb-3">
                    <strong>Color:</strong> 
                    @if($tag->color)
                        <span class="badge" style="background-color: {{ $tag->color }}">
                            {{ $tag->color }}
                        </span>
                    @else
                        N/A
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Created:</strong> {{ $tag->created_at->format('M d, Y H:i') }}
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong> {{ $tag->updated_at->format('M d, Y H:i') }}
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tag?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Tasks with this Tag ({{ $tag->tasks->count() }})
            </div>
            <div class="card-body">
                @if($tag->tasks->isEmpty())
                    <p class="text-muted">No tasks found with this tag.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tag->tasks as $task)
                                    <tr>
                                        <td>{{ $task->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.tasks.show', $task) }}">
                                                {{ $task->title }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($task->completed)
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 