@extends('frontend.layouts.app')

@section('title', 'Tasks')

@section('content')
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">Tasks</h2>
            </div>
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i>
                        Create New Task
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th class="w-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tasks.show', $task) }}" class="text-reset">{{ $task->title }}</a>
                                        </td>
                                        <td>
                                            @if($task->category)
                                                <a href="{{ route('categories.show', $task->category) }}" class="text-reset">{{ $task->category->name }}</a>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                                        </td>
                                        <td>
                                            @if($task->completed)
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-icon btn-sm" title="Edit task">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-sm text-danger" onclick="return confirm('Are you sure you want to delete this task?')" title="Delete task">
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
                    <div class="mt-4">
                        {{ $tasks->links() }}
                    </div>
                @else
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="ti ti-clipboard-list"></i>
                        </div>
                        <p class="empty-title">No tasks found</p>
                        <p class="empty-subtitle text-muted">
                            Get started by creating a new task
                        </p>
                        <div class="empty-action">
                            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus"></i>
                                Create New Task
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 