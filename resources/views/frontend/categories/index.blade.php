@extends('tablar::page')

@section('title', 'Categories')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Categories
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="{{ route('categories.create') }}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Create Category
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
                @foreach($categories as $category)
                    <div class="col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">
                                    <span class="avatar me-2" style="background-color: {{ $category->color }}">
                                        {!! $category->icon ?? '📁' !!}
                                    </span>
                                    {{ $category->name }}
                                </div>
                                <div class="card-actions">
                                    <div class="dropdown">
                                        <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="12" cy="12" r="1" />
                                                <circle cx="12" cy="19" r="1" />
                                                <circle cx="12" cy="5" r="1" />
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('categories.show', $category) }}" class="dropdown-item">
                                                View Tasks
                                            </a>
                                            <a href="{{ route('categories.edit', $category) }}" class="dropdown-item">
                                                Edit
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this category?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Total Tasks</div>
                                                <div class="datagrid-content">{{ $category->total_tasks_count }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Pending Tasks</div>
                                                <div class="datagrid-content">{{ $category->tasks_count }}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Completed Tasks</div>
                                                <div class="datagrid-content">{{ $category->completed_tasks_count }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('categories.show', $category) }}" class="btn btn-link">View Tasks</a>
                                    <div class="ms-auto">
                                        <span class="text-muted">Created {{ $category->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
@endsection 