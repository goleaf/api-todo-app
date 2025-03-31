@extends('admin.layouts.app')

@section('title', 'Tags')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Tags</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Tags</span>
        <a href="{{ route('admin.tags.create') }}" class="btn btn-sm btn-primary">Add Tag</a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('admin.tags.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="user_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
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
                        <th>Name</th>
                        <th>User</th>
                        <th>Color</th>
                        <th>Tasks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>{{ $tag->user->name }}</td>
                            <td>
                                @if($tag->color)
                                    <span class="badge" style="background-color: {{ $tag->color }}">
                                        {{ $tag->color }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $tag->tasks->count() }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.tags.show', $tag) }}" class="btn btn-sm btn-info" dusk="view-tag-{{ $tag->id }}">View</a>
                                    <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-warning" dusk="edit-tag-{{ $tag->id }}">Edit</a>
                                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tag?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" dusk="delete-tag-{{ $tag->id }}">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No tags found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $tags->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection 