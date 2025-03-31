@extends('admin.layouts.app')

@section('title', $isEdit ? 'Edit User' : 'Create User')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active">{{ $isEdit ? 'Edit ' . $user->name : 'Create' }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">{{ $isEdit ? 'Edit User' : 'Create New User' }}</div>
    <div class="card-body">
        <form id="userForm" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $isEdit ? $user->name : '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="name-error">Please provide a name.</div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="email-error">Please provide a valid email address.</div>
            </div>
            
            @if(!$isEdit)
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="password-error">Password must be at least 8 characters.</div>
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                <div class="invalid-feedback" id="confirm-password-error">Passwords do not match.</div>
            </div>
            @else
            <div class="mb-3">
                <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="password-error">Password must be at least 8 characters.</div>
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                <div class="invalid-feedback" id="confirm-password-error">Passwords do not match.</div>
            </div>
            @endif
            
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" {{ old('role', $isEdit ? $user->role->value : 'user') == $role->value ? 'selected' : '' }}>
                            {{ ucfirst($role->value) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="role-error">Please select a role.</div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update User' : 'Create User' }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('userForm');
        
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate name
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                nameInput.classList.add('is-invalid');
                document.getElementById('name-error').style.display = 'block';
                isValid = false;
            } else {
                nameInput.classList.remove('is-invalid');
                document.getElementById('name-error').style.display = 'none';
            }
            
            // Validate email
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value.trim())) {
                emailInput.classList.add('is-invalid');
                document.getElementById('email-error').style.display = 'block';
                isValid = false;
            } else {
                emailInput.classList.remove('is-invalid');
                document.getElementById('email-error').style.display = 'none';
            }
            
            // Validate password if creating new user
            @if(!$isEdit)
            const passwordInput = document.getElementById('password');
            if (passwordInput.value.length < 8) {
                passwordInput.classList.add('is-invalid');
                document.getElementById('password-error').style.display = 'block';
                isValid = false;
            } else {
                passwordInput.classList.remove('is-invalid');
                document.getElementById('password-error').style.display = 'none';
            }
            
            // Validate password confirmation
            const confirmInput = document.getElementById('password_confirmation');
            if (confirmInput.value !== passwordInput.value) {
                confirmInput.classList.add('is-invalid');
                document.getElementById('confirm-password-error').style.display = 'block';
                isValid = false;
            } else {
                confirmInput.classList.remove('is-invalid');
                document.getElementById('confirm-password-error').style.display = 'none';
            }
            @endif
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>
@endsection 