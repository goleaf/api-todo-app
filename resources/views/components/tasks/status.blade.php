@props(['status'])

@php
$statusColors = [
    'pending' => 'bg-yellow-100 text-yellow-800',
    'in_progress' => 'bg-blue-100 text-blue-800',
    'completed' => 'bg-green-100 text-green-800',
    'cancelled' => 'bg-gray-100 text-gray-800',
];

$statusLabels = [
    'pending' => 'Pending',
    'in_progress' => 'In Progress',
    'completed' => 'Completed', 
    'cancelled' => 'Cancelled',
];

$colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
$label = $statusLabels[$status] ?? 'Unknown';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClass}"]) }}>
    {{ $label }}
</span> 