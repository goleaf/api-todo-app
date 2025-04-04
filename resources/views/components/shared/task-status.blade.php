@props(['status'])

@php
$statusClasses = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'in_progress' => 'bg-blue-100 text-blue-800',
    'completed' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-gray-100 text-gray-800',
];

$statusLabels = [
    'pending' => __('tasks.status.pending'),
    'in_progress' => __('tasks.status.in_progress'),
    'completed' => __('tasks.status.completed'),
    'cancelled' => __('tasks.status.cancelled'),
];

$class = $statusClasses[$status] ?? $statusClasses['pending'];
$label = $statusLabels[$status] ?? $statusLabels['pending'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $class]) }}>
    {{ $label }}
</span> 