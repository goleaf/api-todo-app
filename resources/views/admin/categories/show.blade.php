@extends('admin.layouts.app')

@section('title', 'Category Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Categories</a></li>
    <li class="breadcrumb-item active">{{ $category->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                Category Information
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>ID:</strong> {{ $category->id }}
                </div>
                <div class="mb-3">
                    <strong>Name:</strong> {{ $category->name }}
                </div>
                <div class="mb-3">
                    <strong>Description:</strong> {{ $category->description ?? 'N/A' }}
                </div>
                <div class="mb-3">
                    <strong>Type:</strong> {{ $category->type ? $category->type->label() : 'N/A' }}
                </div>
                <div class="mb-3">
                    <strong>User:</strong> {{ $category->user->name }} ({{ $category->user->email }})
                </div>
                <div class="mb-3">
                    <strong>Color:</strong> 
                    @if($category->color)
                        <span class="badge" style="background-color: {{ $category->color }}">
                            {{ $category->color }}
                        </span>
                    @else
                        N/A
                    @endif
                </div>
                <div class="mb-3">
                    <strong>Created:</strong> {{ $category->created_at->format('M d, Y H:i') }}
                </div>
                <div class="mb-3">
                    <strong>Last Updated:</strong> {{ $category->updated_at->format('M d, Y H:i') }}
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Tasks in this Category ({{ $category->tasks->count() }})
            </div>
            <div class="card-body">
                @if($category->tasks->isEmpty())
                    <p class="text-muted">No tasks found in this category.</p>
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
                                @foreach($category->tasks as $task)
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