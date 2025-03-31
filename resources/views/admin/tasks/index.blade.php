@extends('admin.layouts.app')

@section('title', 'Tasks')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Tasks</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Tasks</span>
        <a href="{{ route('admin.tasks.create') }}" class="btn btn-sm btn-primary">Add Task</a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('admin.tasks.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search title or description" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="user_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="incomplete" {{ request('status') == 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select" onchange="this.form.submit()">
                        <option value="">All Priorities</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>User</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>{{ Str::limit($task->title, 30) }}</td>
                            <td>{{ $task->user->name }}</td>
                            <td>
                                @if($task->category)
                                    <span class="badge" style="background-color: {{ $task->category->color ?? '#6c757d' }}">
                                        {{ $task->category->name }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $task->priority_color }}">
                                    {{ $task->priority_label }}
                                </span>
                            </td>
                            <td>
                                @if($task->due_date)
                                    <span class="{{ $task->isOverdue() ? 'text-danger' : '' }}">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.tasks.toggle', $task) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $task->completed ? 'btn-success' : 'btn-warning' }}">
                                        {{ $task->completed ? 'Completed' : 'Pending' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $task->progress == 100 ? 'bg-success' : 'bg-info' }}" 
                                         role="progressbar" 
                                         style="width: {{ $task->progress }}%;" 
                                         aria-valuenow="{{ $task->progress }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ $task->progress }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-info">View</a>
                                    <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No tasks found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $tasks->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection 