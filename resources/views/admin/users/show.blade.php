@extends('admin.layouts.app')

@section('title', 'User Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User Information</h5>
                <div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        @if($user->photo_url)
                            <img src="{{ $user->photo_url }}" alt="{{ $user->name }}" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                                <i class="fas fa-user fa-4x text-secondary"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-9">
                        <h3>{{ $user->name }}</h3>
                        <p class="mb-1"><i class="fas fa-envelope me-2"></i> {{ $user->email }}</p>
                        <p class="mb-1">
                            <span class="badge bg-{{ $user->role->value === 'admin' ? 'danger' : 'primary' }}">
                                {{ ucfirst($user->role->value) }}
                            </span>
                            <span class="badge bg-{{ $user->active ? 'success' : 'secondary' }}">
                                {{ $user->active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                        <p class="text-muted small mb-0">Member since: {{ $user->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Task Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-light rounded p-3">
                            <h3 class="mb-0">{{ $taskStats['total'] }}</h3>
                            <p class="mb-0">Total Tasks</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <h3 class="mb-0">{{ $taskStats['completed'] }}</h3>
                            <p class="mb-0">Completed</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <h3 class="mb-0">{{ $taskStats['incomplete'] }}</h3>
                            <p class="mb-0">Pending</p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="bg-danger bg-opacity-10 rounded p-3">
                            <h3 class="mb-0">{{ $taskStats['overdue'] }}</h3>
                            <p class="mb-0">Overdue</p>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Tasks by Priority</h6>
                        <div class="list-group">
                            @foreach($taskStats['priority'] as $priority => $count)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ ucfirst($priority) }}</span>
                                    <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Tasks by Timeframe</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Due Today</span>
                                <span class="badge bg-info rounded-pill">{{ $taskStats['today'] }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Upcoming</span>
                                <span class="badge bg-secondary rounded-pill">{{ $taskStats['upcoming'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Categories</h5>
            </div>
            <div class="card-body">
                @if($user->categories->count() > 0)
                    <div class="list-group">
                        @foreach($user->categories as $category)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <span class="badge" style="background-color: {{ $category->color }}">•</span>
                                    {{ $category->name }}
                                </span>
                                <span class="badge bg-primary rounded-pill">{{ $category->tasks_count ?? 0 }} tasks</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No categories created</p>
                @endif
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tags</h5>
            </div>
            <div class="card-body">
                @if($user->tags->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($user->tags as $tag)
                            <span class="badge" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No tags created</p>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Account Information</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Created</div>
                        </div>
                        <span class="text-muted">{{ $user->created_at->format('M d, Y H:i') }}</span>
                    </li>
                    @if($user->updated_at->gt($user->created_at))
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Last Updated</div>
                        </div>
                        <span class="text-muted">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                    </li>
                    @endif
                    @if($user->email_verified_at)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Email Verified</div>
                        </div>
                        <span class="text-muted">{{ $user->email_verified_at->format('M d, Y H:i') }}</span>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
</div>
@endsection 