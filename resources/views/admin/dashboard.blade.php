@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Users</h5>
                <p class="card-text display-4">{{ $stats['users_count'] }}</p>
                <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Tasks</h5>
                <p class="card-text display-4">{{ $stats['tasks_count'] }}</p>
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Categories</h5>
                <p class="card-text display-4">{{ $stats['categories_count'] }}</p>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-primary btn-sm">View All</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Completed Tasks</h5>
                <p class="card-text display-4">{{ $stats['completed_tasks_count'] }}</p>
                <div class="progress">
                    @php
                        $completionRate = $stats['tasks_count'] > 0 
                            ? round(($stats['completed_tasks_count'] / $stats['tasks_count']) * 100) 
                            : 0;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionRate }}%">
                        {{ $completionRate }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Task Status Overview
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Completed</td>
                                <td>{{ $stats['completed_tasks_count'] }}</td>
                                <td>
                                    @php
                                        $completionRate = $stats['tasks_count'] > 0 
                                            ? round(($stats['completed_tasks_count'] / $stats['tasks_count']) * 100) 
                                            : 0;
                                    @endphp
                                    {{ $completionRate }}%
                                </td>
                            </tr>
                            <tr>
                                <td>Incomplete</td>
                                <td>{{ $stats['incomplete_tasks_count'] }}</td>
                                <td>
                                    @php
                                        $incompleteRate = $stats['tasks_count'] > 0 
                                            ? round(($stats['incomplete_tasks_count'] / $stats['tasks_count']) * 100) 
                                            : 0;
                                    @endphp
                                    {{ $incompleteRate }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Quick Actions
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add New User</a>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-success">Add New Category</a>
                    <a href="{{ route('admin.tags.create') }}" class="btn btn-info">Add New Tag</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 