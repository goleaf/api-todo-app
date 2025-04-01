@extends('admin.layouts.app')

@section('title', isset($currentFile) ? 'Edit Translations' : 'Create Translation')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.translations.index') }}">Translations</a></li>
    <li class="breadcrumb-item active">{{ isset($currentFile) ? 'Edit ' . $locale : 'Create Translation' }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">{{ isset($currentFile) ? 'Edit Translations: ' . $locale : 'Create New Translation' }}</h5>
        <div>
            <a href="{{ route('admin.translations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Translations
            </a>
        </div>
    </div>
    <div class="card-body">
        @if (empty($fileList) && isset($currentFile))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No translation files found for locale <strong>{{ $locale }}</strong>.
            </div>
        @else
            <div class="row">
                <div class="col-md-3">
                    <div class="card sticky-top" style="top: 20px;">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Files</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach ($fileList ?? [] as $fileName)
                                    <a href="{{ route('admin.translations.edit', ['locale' => $locale, 'file' => $fileName]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ ($currentFile ?? '') === $fileName ? 'active' : '' }}">
                                        <span>
                                            <i class="fas fa-file-code me-2"></i> {{ $fileName }}.php
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-9">
                    @if (isset($currentFile))
                        <form action="{{ route('admin.translations.update', ['locale' => $locale, 'file' => $currentFile]) }}" method="POST">
                            @csrf
                            <div class="card">
                                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">{{ $currentFile }}.php</h5>
                                    <div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if (empty($translations))
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No translations found in this file.
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <input type="text" class="form-control" id="searchTranslations" placeholder="Search translations...">
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover" id="translationsTable">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40%;">Key</th>
                                                        <th>Translation</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($translations as $key => $value)
                                                        <tr>
                                                            <td>
                                                                <code>{{ $key }}</code>
                                                            </td>
                                                            <td>
                                                                <div class="mb-0">
                                                                    @if (strlen($value) > 100)
                                                                        <textarea name="translations[{{ $key }}]" rows="4" class="form-control">{{ $value }}</textarea>
                                                                    @else
                                                                        <input type="text" name="translations[{{ $key }}]" value="{{ $value }}" class="form-control">
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <div class="d-flex justify-content-end mt-3">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i> Save Changes
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    @elseif (!isset($locale))
                        <form action="{{ route('admin.translations.store') }}" method="POST">
                            @csrf
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Create New Translation</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="locale" class="form-label">Locale</label>
                                        <input type="text" name="locale" id="locale" class="form-control @error('locale') is-invalid @enderror" value="{{ old('locale') }}" required placeholder="e.g., en, fr, es">
                                        @error('locale')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Enter the language code (ISO 639-1)</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="file" class="form-label">File Name</label>
                                        <input type="text" name="file" id="file" class="form-control @error('file') is-invalid @enderror" value="{{ old('file') }}" required placeholder="e.g., auth, validation">
                                        @error('file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Enter the translation file name without extension</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Translation Content (JSON)</label>
                                        <textarea name="content" id="content" rows="10" class="form-control @error('content') is-invalid @enderror" required placeholder='{"key": "value", "another_key": "Another value"}'></textarea>
                                        @error('content')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Enter the translation content in JSON format</div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-end mt-3">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save me-2"></i> Create Translation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Select a file from the list to edit translations.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchTranslations');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const table = document.getElementById('translationsTable');
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const key = row.querySelector('td:first-child').textContent.toLowerCase();
                    const value = row.querySelector('td:last-child input, td:last-child textarea').value.toLowerCase();
                    
                    if (key.includes(searchValue) || value.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush 