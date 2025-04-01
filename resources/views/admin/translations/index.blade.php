@extends('admin.layouts.app')

@section('title', 'Translation Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Translations</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Translation Management</h5>
        <div>
            <a href="{{ route('admin.translations.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add New Translation
            </a>
        </div>
    </div>
    <div class="card-body">
        @if (isset($message))
            <div class="alert alert-info">{{ $message }}</div>
        @endif
        
        @if (!empty($locales))
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Available Locales</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach ($locales as $locale)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-language me-2"></i> {{ $locale }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.translations.edit', ['locale' => $locale]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Translation Tools</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6>Find Missing Translations</h6>
                                <form action="{{ route('admin.translations.missing') }}" method="GET" class="mt-2">
                                    <div class="row g-2">
                                        <div class="col-md-5">
                                            <select name="reference" class="form-select" required>
                                                <option value="">Reference Locale</option>
                                                @foreach ($locales as $locale)
                                                    <option value="{{ $locale }}" {{ $locale === 'en' ? 'selected' : '' }}>{{ $locale }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-5">
                                            <select name="locale" class="form-select">
                                                <option value="">All Locales</option>
                                                @foreach ($locales as $locale)
                                                    @if ($locale !== 'en')
                                                        <option value="{{ $locale }}">{{ $locale }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <div>
                                <h6>Check Unused Translations</h6>
                                <div class="mt-2">
                                    <a href="{{ route('admin.translations.unused') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-trash-alt me-2"></i> Find Unused
                                    </a>
                                    <small class="text-muted d-block mt-1">This may take a few moments to scan all files.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Quick Start Guide</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-search fa-2x"></i>
                                </div>
                                <div>
                                    <h6>1. Find Missing Translations</h6>
                                    <p class="small text-muted">Identify missing translation keys between locales.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-edit fa-2x"></i>
                                </div>
                                <div>
                                    <h6>2. Edit Translations</h6>
                                    <p class="small text-muted">Manage translations for each locale and file.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-broom fa-2x"></i>
                                </div>
                                <div>
                                    <h6>3. Clean Up Unused</h6>
                                    <p class="small text-muted">Find and remove unused translation keys.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 