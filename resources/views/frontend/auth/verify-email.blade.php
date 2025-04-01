@extends('tablar::page')

@section('title', 'Verify Email')

@section('content')
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="navbar-brand navbar-brand-autodark">
                    <h1>{{ config('app.name') }}</h1>
                </a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Email Verification Required</h2>
                    <p class="text-muted mb-4">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                        link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                    </p>
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success" role="alert">
                            A new verification link has been sent to the email address you provided during registration.
                        </div>
                    @endif
                    <form action="{{ route('verification.send') }}" method="post">
                        @csrf
                        <div class="form-footer">
                            <button type="submit" class="btn btn-primary w-100">
                                Resend Verification Email
                            </button>
                        </div>
                    </form>
                    <form action="{{ route('logout') }}" method="post" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-link w-100">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 