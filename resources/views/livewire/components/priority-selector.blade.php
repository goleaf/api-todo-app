<div class="priority-selector">
    <div class="priority-options">
        <label class="priority-option">
            <input 
                type="radio" 
                name="priority-{{ $todoId }}" 
                value="0" 
                {{ $currentPriority == 0 ? 'checked' : '' }}
                wire:change="setPriority({{ $todoId }}, 0)"
            >
            <span class="priority-label priority-low">Low</span>
        </label>
        <label class="priority-option">
            <input 
                type="radio" 
                name="priority-{{ $todoId }}" 
                value="1" 
                {{ $currentPriority == 1 ? 'checked' : '' }}
                wire:change="setPriority({{ $todoId }}, 1)"
            >
            <span class="priority-label priority-medium">Medium</span>
        </label>
        <label class="priority-option">
            <input 
                type="radio" 
                name="priority-{{ $todoId }}" 
                value="2" 
                {{ $currentPriority == 2 ? 'checked' : '' }}
                wire:change="setPriority({{ $todoId }}, 2)"
            >
            <span class="priority-label priority-high">High</span>
        </label>
    </div>
</div> 