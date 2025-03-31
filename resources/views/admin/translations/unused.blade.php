@extends('admin.layouts.app')

@section('title', 'Unused Translations')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.translations.index') }}">Translations</a></li>
    <li class="breadcrumb-item active">Unused Translations</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Unused Translations</h5>
        <div>
            <a href="{{ route('admin.translations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Translations
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            This shows translation keys that are defined in language files but not used in the application code.
            <strong>Note:</strong> Some translations might be used dynamically or in JavaScript, so verify before removal.
        </div>
        
        @if (empty($unused))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                No unused translations found!
            </div>
        @else
            @foreach ($unused as $locale => $files)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Locale: {{ $locale }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="accordion" id="accordion-{{ $locale }}">
                            @foreach ($files as $file => $keys)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $locale }}-{{ $file }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $locale }}-{{ $file }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $locale }}-{{ $file }}">
                                            <div class="d-flex justify-content-between w-100 me-3">
                                                <span>{{ $file }}.php</span>
                                                <span class="badge bg-warning">{{ count($keys) }} unused</span>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $locale }}-{{ $file }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $locale }}-{{ $file }}" data-bs-parent="#accordion-{{ $locale }}">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Key</th>
                                                            <th>Value</th>
                                                            <th style="width: 120px;">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($keys as $key => $value)
                                                            <tr>
                                                                <td><code>{{ $key }}</code></td>
                                                                <td>{{ $value }}</td>
                                                                <td>
                                                                    <a href="{{ route('admin.translations.edit', ['locale' => $locale, 'file' => $file]) }}" class="btn btn-sm btn-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> Manual review is recommended before removing any translations.
                <ul class="mb-0 mt-2">
                    <li>Check for dynamic usage with variables (e.g., <code>trans($key)</code>)</li>
                    <li>Check for usage in JavaScript files</li>
                    <li>Check for usage in Blade components or includes</li>
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection 