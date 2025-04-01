<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Time Entry Details') }}
            </h2>
            <div>
                <a href="{{ route('time-entries.edit', $timeEntry) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                    Edit
                </a>
                <a href="{{ route('time-entries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to Time Entries
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Time Entry Information</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Task</p>
                                    <p class="mt-1">
                                        <a href="{{ route('tasks.show', $timeEntry->task) }}" class="text-indigo-600 hover:text-indigo-900">{{ $timeEntry->task->title }}</a>
                                    </p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Start Time</p>
                                    <p class="mt-1">{{ $timeEntry->start_time->format('M d, Y H:i') }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">End Time</p>
                                    <p class="mt-1">{{ $timeEntry->end_time ? $timeEntry->end_time->format('M d, Y H:i') : 'In Progress' }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Duration</p>
                                    <p class="mt-1">{{ $timeEntry->end_time ? $timeEntry->duration . ' minutes' : 'Ongoing' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                @if($timeEntry->notes)
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ $timeEntry->notes }}</p>
                                @else
                                    <p class="text-gray-500">No notes provided.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 