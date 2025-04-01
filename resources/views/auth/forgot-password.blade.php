@extends('frontend.layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Forgot password</h2>
                <p class="text-muted mb-4">Enter your email address and we'll send you a password reset link.</p>
                <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="your@email.com" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">
                            Send Reset Link
                        </button>
                    </div>
                </form>
                <div class="text-center text-muted mt-3">
                    Forget it, <a href="{{ route('login') }}">send me back</a> to the sign in screen.
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('status'))
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert("{{ session('status') }}");
        });
    </script>
    @endpush
@endif
@endsection 