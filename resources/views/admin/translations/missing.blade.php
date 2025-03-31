@extends('admin.layouts.app')

@section('title', 'Missing Translations')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.translations.index') }}">Translations</a></li>
    <li class="breadcrumb-item active">Missing Translations</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Missing Translations</h5>
        <div>
            @if (!empty($missing))
                <form action="{{ route('admin.translations.fix') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reference" value="{{ $referenceLocale }}">
                    @if ($targetLocale)
                        <input type="hidden" name="locale" value="{{ $targetLocale }}">
                    @endif
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-magic me-2"></i> Generate Missing Files
                    </button>
                </form>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <form action="{{ route('admin.translations.missing') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <select name="reference" class="form-select" required>
                        <option value="">Reference Locale</option>
                        @foreach ($locales as $locale)
                            <option value="{{ $locale }}" {{ $locale === $referenceLocale ? 'selected' : '' }}>{{ $locale }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <select name="locale" class="form-select">
                        <option value="">All Locales</option>
                        @foreach ($locales as $locale)
                            @if ($locale !== $referenceLocale)
                                <option value="{{ $locale }}" {{ $locale === $targetLocale ? 'selected' : '' }}>{{ $locale }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i> Search
                    </button>
                </div>
            </form>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            @if ($targetLocale)
                Showing missing translations in <strong>{{ $targetLocale }}</strong> compared to <strong>{{ $referenceLocale }}</strong>.
            @else
                Showing missing translations in all locales compared to <strong>{{ $referenceLocale }}</strong>.
            @endif
        </div>
        
        @if (empty($missing))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                No missing translations found!
            </div>
        @else
            @foreach ($missing as $locale => $files)
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
                                                <span class="badge bg-warning">{{ count($keys) }} missing</span>
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
                                                            <th>Value in {{ $referenceLocale }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($keys as $key => $value)
                                                            <tr>
                                                                <td><code>{{ $key }}</code></td>
                                                                <td>{{ $value }}</td>
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
            
            <div class="d-flex justify-content-end">
                <form action="{{ route('admin.translations.fix') }}" method="POST">
                    @csrf
                    <input type="hidden" name="reference" value="{{ $referenceLocale }}">
                    @if ($targetLocale)
                        <input type="hidden" name="locale" value="{{ $targetLocale }}">
                    @endif
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-magic me-2"></i> Generate Missing Files
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection 