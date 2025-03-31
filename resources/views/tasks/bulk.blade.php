@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">{{ __('Bulk Task Processing') }}</h1>

<p class="mb-4 text-gray-600">Process multiple tasks at once efficiently with our bulk processor.</p>

<livewire:task-bulk-processor />
@endsection 