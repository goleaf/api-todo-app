@extends('admin.layouts.app')

@section('title', 'Users')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Users</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">Add User</a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>@sortablelink('id', 'ID')</th>
                        <th>@sortablelink('name', 'Name')</th>
                        <th>@sortablelink('email', 'Email')</th>
                        <th>@sortablelink('created_at', 'Registered')</th>
                        <th>Tasks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>{{ $user->tasks->count() }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info" dusk="view-user-{{ $user->id }}">View</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" dusk="edit-user-{{ $user->id }}">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" dusk="delete-user-{{ $user->id }}">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No users found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection 