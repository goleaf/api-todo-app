@extends('admin.layouts.app')

@section('title', 'Edit Task')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Task</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.tasks.update', $task) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_id" class="form-label">Assign To User</label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="priority" class="form-label">Priority</label>
                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->value }}" {{ old('priority', $task->priority) == $priority->value ? 'selected' : '' }}>
                                {{ $priority->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('priority')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                    @error('due_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="progress" class="form-label">Progress (%)</label>
                    <input type="number" class="form-control @error('progress') is-invalid @enderror" id="progress" name="progress" min="0" max="100" value="{{ old('progress', $task->progress) }}">
                    @error('progress')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="completed" name="completed" value="1" {{ old('completed', $task->completed) ? 'checked' : '' }}>
                    <label class="form-check-label" for="completed">
                        Mark as completed
                    </label>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                <select class="form-select @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $task->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Hold Ctrl/Cmd to select multiple tags</small>
                @error('tags')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Task</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // When user is selected, fetch user's categories and tags
        const userSelect = document.getElementById('user_id');
        const categorySelect = document.getElementById('category_id');
        const tagSelect = document.getElementById('tags');
        const currentCategoryId = {{ $task->category_id ?? 'null' }};
        const currentTagIds = {{ json_encode($task->tags->pluck('id')) }};
        
        userSelect.addEventListener('change', function() {
            const userId = this.value;
            if (!userId) return;
            
            // Fetch categories for user
            fetch(`/admin/users/${userId}/categories`)
                .then(response => response.json())
                .then(data => {
                    // Clear current options
                    categorySelect.innerHTML = '<option value="">No Category</option>';
                    
                    // Add new options
                    data.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        if (category.id === currentCategoryId) {
                            option.selected = true;
                        }
                        categorySelect.appendChild(option);
                    });
                });
                
            // Fetch tags for user
            fetch(`/admin/users/${userId}/tags`)
                .then(response => response.json())
                .then(data => {
                    // Clear current options
                    tagSelect.innerHTML = '';
                    
                    // Add new options
                    data.forEach(tag => {
                        const option = document.createElement('option');
                        option.value = tag.id;
                        option.textContent = tag.name;
                        if (currentTagIds.includes(tag.id)) {
                            option.selected = true;
                        }
                        tagSelect.appendChild(option);
                    });
                });
        });
    });
</script>
@endpush 