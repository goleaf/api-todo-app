@props(['date'])

@php
    $now = now();
    $dueDate = $date ? \Carbon\Carbon::parse($date) : null;
    
    if (!$dueDate) {
        $color = 'text-gray-500';
    } elseif ($dueDate->isPast()) {
        $color = 'text-red-600';
    } elseif ($dueDate->isToday()) {
        $color = 'text-yellow-600';
    } elseif ($dueDate->isTomorrow()) {
        $color = 'text-orange-600';
    } elseif ($dueDate->diffInDays($now) <= 7) {
        $color = 'text-blue-600';
    } else {
        $color = 'text-green-600';
    }

    $icon = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 ' . $color]) }}>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
    </svg>
    <span>
        @if($formattedDate)
            {{ $formattedDate }}
        @else
            {{ $label }}
        @endif
    </span>
</div> 