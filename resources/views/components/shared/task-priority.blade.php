@props(['priority'])

@php
$priorityClasses = [
    'low' => 'bg-green-100 text-green-800',
    'medium' => 'bg-blue-100 text-blue-800',
    'high' => 'bg-orange-100 text-orange-800',
    'urgent' => 'bg-red-100 text-red-800',
];

$priorityLabels = [
    'low' => __('tasks.priority.low'),
    'medium' => __('tasks.priority.medium'),
    'high' => __('tasks.priority.high'),
    'urgent' => __('tasks.priority.urgent'),
];

$class = $priorityClasses[$priority] ?? $priorityClasses['medium'];
$label = $priorityLabels[$priority] ?? $priorityLabels['medium'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $class]) }}>
    {{ $label }}
</span> 