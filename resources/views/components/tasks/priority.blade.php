@props(['priority'])

@php
$priorityColors = [
    'low' => 'bg-green-100 text-green-800',
    'medium' => 'bg-yellow-100 text-yellow-800',
    'high' => 'bg-orange-100 text-orange-800',
    'urgent' => 'bg-red-100 text-red-800',
];

$priorityLabels = [
    'low' => 'Low',
    'medium' => 'Medium',
    'high' => 'High',
    'urgent' => 'Urgent',
];

$colorClass = $priorityColors[$priority] ?? 'bg-gray-100 text-gray-800';
$label = $priorityLabels[$priority] ?? 'Unknown';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClass}"]) }}>
    {{ $label }}
</span> 