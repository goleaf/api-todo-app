@extends('tablar::page')

@section('title', $category->name)

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle">
                        Category
                    </div>
                    <h2 class="page-title">
                        <span class="avatar avatar-sm" style="background-color: {{ $category->color }}">{{ $category->icon ?? '📁' }}</span>
                        {{ $category->name }}
                    </h2>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-list">
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category? This will also delete all associated tasks.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <line x1="4" y1="7" x2="20" y2="7" />
                                    <line x1="10" y1="11" x2="10" y2="17" />
                                    <line x1="14" y1="11" x2="14" y2="17" />
                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                </svg>
                                Delete
                            </button>
                        </form>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                                <line x1="13.5" y1="6.5" x2="17.5" y2="10.5" />
                            </svg>
                            Edit
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Category Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Name</div>
                                    <div class="datagrid-content">{{ $category->name }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Color</div>
                                    <div class="datagrid-content">
                                        <span class="avatar avatar-sm" style="background-color: {{ $category->color }}"></span>
                                        {{ $category->color }}
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Icon</div>
                                    <div class="datagrid-content">{{ $category->icon ?? '📁' }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Created</div>
                                    <div class="datagrid-content">{{ $category->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Last Updated</div>
                                    <div class="datagrid-content">{{ $category->updated_at->diffForHumans() }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">Total Tasks</div>
                                    <div class="datagrid-content">{{ $category->tasks_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tasks in this Category</h3>
                            <div class="card-actions">
                                <a href="{{ route('tasks.create', ['category' => $category->id]) }}" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    New Task
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Due Date</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th class="w-1"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($category->tasks as $task)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('tasks.show', $task) }}" class="text-reset">{{ $task->title }}</a>
                                                </td>
                                                <td>
                                                    @if($task->due_date)
                                                        <span class="{{ $task->is_overdue ? 'text-danger' : '' }}">
                                                            {{ $task->due_date->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">No due date</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @switch($task->priority)
                                                        @case('high')
                                                            <span class="badge bg-danger">High</span>
                                                            @break
                                                        @case('medium')
                                                            <span class="badge bg-warning">Medium</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-info">Low</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @if($task->completed_at)
                                                        <span class="badge bg-success">Completed</span>
                                                    @else
                                                        <span class="badge bg-primary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('tasks.show', $task) }}" class="btn btn-icon" title="View task">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                            <circle cx="12" cy="12" r="2" />
                                                            <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="empty">
                                                        <div class="empty-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                <line x1="12" y1="5" x2="12" y2="19" />
                                                                <line x1="5" y1="12" x2="19" y2="12" />
                                                            </svg>
                                                        </div>
                                                        <p class="empty-title">No tasks found</p>
                                                        <p class="empty-subtitle text-muted">
                                                            Get started by creating a new task in this category.
                                                        </p>
                                                        <div class="empty-action">
                                                            <a href="{{ route('tasks.create', ['category' => $category->id]) }}" class="btn btn-primary">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                                    <line x1="12" y1="5" x2="12" y2="19" />
                                                                    <line x1="5" y1="12" x2="19" y2="12" />
                                                                </svg>
                                                                Create Task
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 