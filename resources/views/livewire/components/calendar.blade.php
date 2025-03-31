<div class="calendar-container">
    <div class="calendar-header">
        <button wire:click="calendarPrevious" class="calendar-nav-button">
            &larr;
        </button>
        <h3>{{ $calendarData['monthName'] }} {{ $calendarData['year'] }}</h3>
        <button wire:click="calendarNext" class="calendar-nav-button">
            &rarr;
        </button>
    </div>
    
    <div class="calendar-weekdays">
        <div class="calendar-weekday">Sun</div>
        <div class="calendar-weekday">Mon</div>
        <div class="calendar-weekday">Tue</div>
        <div class="calendar-weekday">Wed</div>
        <div class="calendar-weekday">Thu</div>
        <div class="calendar-weekday">Fri</div>
        <div class="calendar-weekday">Sat</div>
    </div>
    
    <div class="calendar-grid">
        @foreach($calendarData['days'] as $day)
            <div 
                wire:click="$dispatch('dateSelected', '{{ $day['date'] }}')"
                class="calendar-day {{ !$day['isCurrentMonth'] ? 'calendar-other-month' : '' }} 
                              {{ $day['isToday'] ? 'calendar-today' : '' }}
                              {{ $day['isSelected'] ? 'calendar-selected' : '' }}"
            >
                <div class="calendar-date">{{ $day['day'] }}</div>
                @if($day['todoCount'] > 0)
                    <div class="todo-indicator">{{ $day['todoCount'] }}</div>
                @endif
            </div>
        @endforeach
    </div>
    
    @if($selectedDate)
        <div class="selected-date-info">
            <p>Showing todos for {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</p>
            <button wire:click="clearDateSelection" class="clear-date-button">Clear</button>
        </div>
    @endif
</div> 