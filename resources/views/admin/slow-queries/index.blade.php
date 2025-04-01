@extends('admin.layouts.app')

@section('title', 'Slow Queries')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Slow Queries</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Slow Queries</h5>
        <div>
            <form action="{{ route('admin.slow-queries.clear-all') }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear all slow query logs? This action cannot be undone.')">
                    <i class="fas fa-trash me-2"></i> Clear All
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" action="{{ route('admin.slow-queries.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search" placeholder="Search queries..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="time_filter" class="form-select">
                                <option value="">All Time</option>
                                <option value="today" {{ request('time_filter') === 'today' ? 'selected' : '' }}>Today</option>
                                <option value="week" {{ request('time_filter') === 'week' ? 'selected' : '' }}>This Week</option>
                                <option value="month" {{ request('time_filter') === 'month' ? 'selected' : '' }}>This Month</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">Min Time</span>
                                <input type="number" class="form-control" name="min_time" placeholder="ms" value="{{ request('min_time') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="analyzed" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request()->has('analyzed') && request('analyzed') == '1' ? 'selected' : '' }}>Analyzed</option>
                                <option value="0" {{ request()->has('analyzed') && request('analyzed') == '0' ? 'selected' : '' }}>Not Analyzed</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($slowQueries->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> No slow queries found. Your application is performing well, or the threshold may need adjustment.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Query</th>
                            <th>Time (ms)</th>
                            <th>Connection</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slowQueries as $query)
                            <tr>
                                <td>{{ $query->id }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;">
                                        {{ \Illuminate\Support\Str::limit($query->raw_sql, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $query->time > 2000 ? 'danger' : ($query->time > 1000 ? 'warning' : 'info') }}">
                                        {{ number_format($query->time, 2) }}
                                    </span>
                                </td>
                                <td>{{ $query->connection }}</td>
                                <td>{{ $query->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    @if($query->is_analyzed)
                                        <span class="badge bg-success">Analyzed</span>
                                    @else
                                        <span class="badge bg-danger">Not Analyzed</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.slow-queries.show', $query) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$query->is_analyzed)
                                            <form action="{{ route('admin.slow-queries.mark-analyzed', $query) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.slow-queries.destroy', $query) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this slow query log?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $slowQueries->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 