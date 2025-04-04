@props(['userId' => null])

@php
    $userId = $userId ?? Auth::id();
    
    $totalTasks = App\Models\Task::where('user_id', $userId)->count();
    $completedTasks = App\Models\Task::where('user_id', $userId)->where('completed', true)->count();
    $pendingTasks = App\Models\Task::where('user_id', $userId)->where('completed', false)->count();
    $dueTodayTasks = App\Models\Task::where('user_id', $userId)
        ->whereDate('due_date', today())
        ->where('completed', false)
        ->count();
    $overdueTasks = App\Models\Task::where('user_id', $userId)
        ->where('due_date', '<', now())
        ->where('completed', false)
        ->count();
    
    $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
@endphp

<x-shared.components.ui.card>
    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Task Overview') }}</h3>
    
    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">{{ __('Total Tasks') }}</span>
            <span class="font-medium">{{ $totalTasks }}</span>
        </div>
        
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">{{ __('Completed') }}</span>
            <span class="font-medium text-green-600">{{ $completedTasks }}</span>
        </div>
        
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">{{ __('Pending') }}</span>
            <span class="font-medium text-yellow-600">{{ $pendingTasks }}</span>
        </div>
        
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">{{ __('Due Today') }}</span>
            <span class="font-medium text-orange-600">{{ $dueTodayTasks }}</span>
        </div>
        
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">{{ __('Overdue') }}</span>
            <span class="font-medium text-red-600">{{ $overdueTasks }}</span>
        </div>
        
        <div class="pt-2">
            <div class="flex justify-between items-center mb-1">
                <span class="text-sm text-gray-600">{{ __('Completion Rate') }}</span>
                <span class="text-sm font-medium">{{ $completionRate }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $completionRate }}%"></div>
            </div>
        </div>
    </div>
</x-shared.components.ui.card> 