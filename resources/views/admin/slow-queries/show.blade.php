@extends('admin.layouts.app')

@section('title', 'Slow Query Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.slow-queries.index') }}">Slow Queries</a></li>
    <li class="breadcrumb-item active">Query #{{ $slowQuery->id }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Slow Query Details #{{ $slowQuery->id }}</h5>
        <div>
            <a href="{{ route('admin.slow-queries.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to List
            </a>
            @if(!$slowQuery->is_analyzed)
                <form action="{{ route('admin.slow-queries.mark-analyzed', $slowQuery) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i> Mark as Analyzed
                    </button>
                </form>
            @endif
            <form action="{{ route('admin.slow-queries.destroy', $slowQuery) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this slow query log?')">
                    <i class="fas fa-trash me-2"></i> Delete
                </button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-{{ $slowQuery->is_analyzed ? 'success' : 'warning' }}">
                    <i class="fas {{ $slowQuery->is_analyzed ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-2"></i>
                    This query took <strong>{{ number_format($slowQuery->time, 2) }} ms</strong> to execute and is {{ $slowQuery->is_analyzed ? 'marked as analyzed' : 'not yet analyzed' }}.
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Query Information</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Execution Time</dt>
                            <dd class="col-sm-8">{{ number_format($slowQuery->time, 2) }} ms</dd>

                            <dt class="col-sm-4">Connection</dt>
                            <dd class="col-sm-8">{{ $slowQuery->connection }}</dd>

                            <dt class="col-sm-4">Connection Name</dt>
                            <dd class="col-sm-8">{{ $slowQuery->connection_name ?: 'Default' }}</dd>

                            <dt class="col-sm-4">Date/Time</dt>
                            <dd class="col-sm-8">{{ $slowQuery->created_at->format('Y-m-d H:i:s') }}</dd>

                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-{{ $slowQuery->is_analyzed ? 'success' : 'danger' }}">
                                    {{ $slowQuery->is_analyzed ? 'Analyzed' : 'Not Analyzed' }}
                                </span>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Performance Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-center" style="height: 120px;">
                            <div class="text-center">
                                <div style="width: 120px; height: 120px; margin: 0 auto;" class="position-relative">
                                    <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 2;">
                                        <h3>{{ number_format($slowQuery->time, 0) }}</h3>
                                        <span class="text-muted">ms</span>
                                    </div>
                                    <div class="position-absolute top-0 start-0 w-100 h-100">
                                        <svg viewBox="0 0 36 36" class="circular-chart 
                                            {{ $slowQuery->time < 1000 ? 'good' : ($slowQuery->time < 2000 ? 'warning' : 'danger') }}">
                                            <path class="circle-bg"
                                                d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                            <path class="circle"
                                                stroke-dasharray="{{ min(100, $slowQuery->time / 50) }}, 100"
                                                d="M18 2.0845
                                                a 15.9155 15.9155 0 0 1 0 31.831
                                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-{{ $slowQuery->time < 1000 ? 'success' : ($slowQuery->time < 2000 ? 'warning' : 'danger') }}">
                                        {{ $slowQuery->time < 1000 ? 'Good' : ($slowQuery->time < 2000 ? 'Warning' : 'Critical') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">SQL Query</h5>
                    </div>
                    <div class="card-body">
                        <pre class="p-3 bg-light rounded"><code>{{ $slowQuery->raw_sql }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Bindings</h5>
                    </div>
                    <div class="card-body">
                        <pre class="p-3 bg-light rounded"><code>{{ $slowQuery->bindings }}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        @if($slowQuery->recommendation)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">AI Recommendations</h5>
                    </div>
                    <div class="card-body">
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($slowQuery->recommendation)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Optimization Suggestions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <strong>Add Indexes:</strong> Check if all columns in WHERE, JOIN, and ORDER BY clauses are properly indexed.
                            </li>
                            <li class="list-group-item">
                                <strong>Check for N+1 Issues:</strong> If this query is executed within a loop, consider eager loading relationships.
                            </li>
                            <li class="list-group-item">
                                <strong>Query Optimization:</strong> Consider rewriting to avoid subqueries, use JOINs efficiently, and limit result sets.
                            </li>
                            <li class="list-group-item">
                                <strong>Caching:</strong> For queries that don't change frequently, consider implementing caching.
                            </li>
                            <li class="list-group-item">
                                <strong>Database Schema:</strong> Review table design for normalization and appropriate column types.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .circular-chart {
        display: block;
        width: 100%;
        height: 100%;
    }

    .circle-bg {
        fill: none;
        stroke: #eee;
        stroke-width: 3.8;
    }

    .circle {
        fill: none;
        stroke-width: 2.8;
        stroke-linecap: round;
    }

    .good .circle {
        stroke: #4caf50;
    }

    .warning .circle {
        stroke: #ff9800;
    }

    .danger .circle {
        stroke: #f44336;
    }
</style>
@endsection 