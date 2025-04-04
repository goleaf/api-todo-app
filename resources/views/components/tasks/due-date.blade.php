@props(['date'])

@php
    $now = now();
    $dueDate = $date ? \Carbon\Carbon::parse($date) : null;
    
    if (!$dueDate) {
        $color = 'text-gray-500';
        $label = __('No due date');
        $formattedDate = null;
    } elseif ($dueDate->isPast()) {
        $color = 'text-red-600';
        $label = __('Overdue');
        $formattedDate = $dueDate->format('M d, Y');
    } elseif ($dueDate->isToday()) {
        $color = 'text-yellow-600';
        $label = __('Today');
        $formattedDate = $dueDate->format('h:i A');
    } elseif ($dueDate->isTomorrow()) {
        $color = 'text-orange-600';
        $label = __('Tomorrow');
        $formattedDate = $dueDate->format('h:i A');
    } elseif ($dueDate->diffInDays($now) <= 7) {
        $color = 'text-blue-600';
        $label = $dueDate->format('l'); // Day name
        $formattedDate = $dueDate->format('M d');
    } else {
        $color = 'text-green-600';
        $label = __('Upcoming');
        $formattedDate = $dueDate->format('M d, Y');
    }
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