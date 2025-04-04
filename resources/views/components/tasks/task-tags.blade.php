@props(['tags'])

@if($tags && $tags->count() > 0)
    <div class="flex flex-wrap gap-1">
        @foreach($tags as $tag)
            <a href="{{ route('tasks.index', ['tag' => $tag->id]) }}" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs" style="background-color: {{ $tag->color ?? '#e5e7eb' }}; color: {{ getContrastColor($tag->color ?? '#e5e7eb') }};">
                #{{ $tag->name }}
            </a>
        @endforeach
    </div>
@endif

@php
function getContrastColor($hexColor) {
    // If no color or invalid format, return black
    if (!$hexColor || strlen($hexColor) < 7) {
        return '#000000';
    }
    
    // Extract red, green, and blue components
    $r = hexdec(substr($hexColor, 1, 2));
    $g = hexdec(substr($hexColor, 3, 2));
    $b = hexdec(substr($hexColor, 5, 2));
    
    // Calculate luminance using the sRGB color space formula
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    
    // Return black for light colors and white for dark colors
    return $luminance > 0.5 ? '#000000' : '#ffffff';
}
@endphp 