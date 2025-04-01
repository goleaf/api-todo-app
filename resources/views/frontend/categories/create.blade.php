@extends('tablar::page')

@section('title', 'Create Category')

@section('content')
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Create Category
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
                    <form action="{{ route('categories.store') }}" method="POST" class="card">
                        @csrf
                        <div class="card-header">
                            <h3 class="card-title">Category Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label required">Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Color</label>
                                <div class="row g-2">
                                    <div class="col-auto">
                                        <input type="color" name="color" class="form-control form-control-color @error('color') is-invalid @enderror" value="{{ old('color', '#206bc4') }}" title="Choose category color">
                                    </div>
                                    <div class="col">
                                        <input type="text" name="color_hex" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', '#206bc4') }}" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#206bc4">
                                    </div>
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Choose a color for the category or enter a hex color code.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Icon</label>
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="📁">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn" data-bs-toggle="popover" data-bs-html="true" title="Suggested Icons" data-bs-content="📁 📂 📊 📈 📝 ✅ ⭐ 🎯<br>Click to copy">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <circle cx="12" cy="12" r="9" />
                                                <line x1="12" y1="8" x2="12" y2="12" />
                                                <line x1="12" y1="16" x2="12.01" y2="16" />
                                            </svg>
                                            Icons
                                        </button>
                                    </div>
                                </div>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">Choose an emoji to represent this category. Leave empty to use the default icon.</small>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <div class="d-flex">
                                <a href="{{ route('categories.index') }}" class="btn btn-link">Cancel</a>
                                <button type="submit" class="btn btn-primary ms-auto">Create Category</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    trigger: 'focus'
                });
            });

            // Sync color inputs
            const colorPicker = document.querySelector('input[name="color"]');
            const colorText = document.querySelector('input[name="color_hex"]');

            colorPicker.addEventListener('input', function(e) {
                colorText.value = e.target.value;
            });

            colorText.addEventListener('input', function(e) {
                if (e.target.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                    colorPicker.value = e.target.value;
                }
            });
        });
    </script>
@endpush 