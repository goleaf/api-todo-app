@extends('tablar::page')

@section('title', 'System Settings')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        System Settings
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form action="{{ route('admin.settings.update') }}" method="POST" class="card">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h3 class="card-title">General Settings</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" name="site_name" class="form-control @error('site_name') is-invalid @enderror" value="{{ old('site_name', $settings['site_name'] ?? config('app.name')) }}">
                                @error('site_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Default Task Due Days</label>
                                <input type="number" name="default_task_due_days" class="form-control @error('default_task_due_days') is-invalid @enderror" value="{{ old('default_task_due_days', $settings['default_task_due_days'] ?? 7) }}" min="1">
                                @error('default_task_due_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Number of days to set as default due date when creating tasks</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" name="enable_registration" class="form-check-input" {{ old('enable_registration', $settings['enable_registration'] ?? true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Enable User Registration</span>
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" name="require_email_verification" class="form-check-input" {{ old('require_email_verification', $settings['require_email_verification'] ?? true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Require Email Verification</span>
                                </label>
                            </div>
                        </div>
                        <div class="card-header border-top">
                            <h3 class="card-title">Email Settings</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">From Email Address</label>
                                <input type="email" name="mail_from_address" class="form-control @error('mail_from_address') is-invalid @enderror" value="{{ old('mail_from_address', $settings['mail_from_address'] ?? config('mail.from.address')) }}">
                                @error('mail_from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">From Name</label>
                                <input type="text" name="mail_from_name" class="form-control @error('mail_from_name') is-invalid @enderror" value="{{ old('mail_from_name', $settings['mail_from_name'] ?? config('mail.from.name')) }}">
                                @error('mail_from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" name="send_due_date_reminders" class="form-check-input" {{ old('send_due_date_reminders', $settings['send_due_date_reminders'] ?? true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Send Due Date Reminders</span>
                                </label>
                            </div>
                        </div>
                        <div class="card-header border-top">
                            <h3 class="card-title">Task Settings</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Default Task Priority</label>
                                <select name="default_task_priority" class="form-select @error('default_task_priority') is-invalid @enderror">
                                    <option value="low" {{ old('default_task_priority', $settings['default_task_priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('default_task_priority', $settings['default_task_priority'] ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('default_task_priority', $settings['default_task_priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('default_task_priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" name="allow_task_attachments" class="form-check-input" {{ old('allow_task_attachments', $settings['allow_task_attachments'] ?? true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Allow Task Attachments</span>
                                </label>
                            </div>

                            <div class="mb-3">
                                <label class="form-check form-switch">
                                    <input type="checkbox" name="allow_task_time_tracking" class="form-check-input" {{ old('allow_task_time_tracking', $settings['allow_task_time_tracking'] ?? true) ? 'checked' : '' }}>
                                    <span class="form-check-label">Allow Task Time Tracking</span>
                                </label>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn btn-primary">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 