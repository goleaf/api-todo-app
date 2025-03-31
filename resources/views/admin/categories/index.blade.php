@extends('admin.layouts.app')

@section('title', 'Categories')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Categories</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Categories</span>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">Add Category</a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3">
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
                        <th>@sortablelink('id', 'ID')</th>
                        <th>@sortablelink('name', 'Name')</th>
                        <th>@sortablelink('type', 'Type')</th>
                        <th>@sortablelink('user.name', 'User')</th>
                        <th>Color</th>
                        <th>Tasks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->type ? $category->type->label() : 'N/A' }}</td>
                            <td>{{ $category->user->name }}</td>
                            <td>
                                @if($category->color)
                                    <span class="badge" style="background-color: {{ $category->color }}">
                                        {{ $category->color }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $category->tasks->count() }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-info" dusk="view-category-{{ $category->id }}">View</a>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning" dusk="edit-category-{{ $category->id }}">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" dusk="delete-category-{{ $category->id }}">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No categories found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $categories->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection 