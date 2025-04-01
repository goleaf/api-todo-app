@extends('admin.layouts.app')

@section('title', $isEdit ? 'Edit Tag' : 'Create Tag')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.tags.index') }}">Tags</a></li>
    <li class="breadcrumb-item active">{{ $isEdit ? 'Edit ' . $tag->name : 'Create' }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">{{ $isEdit ? 'Edit Tag' : 'Create New Tag' }}</div>
    <div class="card-body">
        <form action="{{ $isEdit ? route('admin.tags.update', $tag) : route('admin.tags.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $isEdit ? $tag->name : '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="user_id" class="form-label">User</label>
                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $isEdit ? $tag->user_id : '') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $isEdit ? $tag->color : '#4f46e5') }}">
                @error('color')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Tag' : 'Create Tag' }}</button>
                <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection 