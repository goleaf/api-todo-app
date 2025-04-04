@extends('layouts.app')

@section('title', __('Tasks'))

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <x-ui.page-header :title="$pageTitle ?? __('All Tasks')">
            <x-slot name="actions">
                <x-ui.button :href="route('tasks.create')" variant="primary" icon="<path fill-rule='evenodd' d='M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z' clip-rule='evenodd' />">
                    {{ __('New Task') }}
                </x-ui.button>
            </x-slot>
        </x-ui.page-header>

        <div class="mb-6">
            <x-tasks.task-filters :categories="$categories" :selectedStatus="$selectedStatus ?? null" :selectedCategory="$selectedCategory ?? null" :selectedPriority="$selectedPriority ?? null" />
        </div>

        @if($tasks->isEmpty())
            <x-ui.empty-state 
                :message="__('No tasks found')" 
                icon="<path stroke-linecap='round' stroke-linejoin='round' d='M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' />"
            >
                <x-slot name="action">
                    <x-ui.button :href="route('tasks.create')" variant="primary">
                        {{ __('Create your first task') }}
                    </x-ui.button>
                </x-slot>
            </x-ui.empty-state>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tasks as $task)
                    <x-tasks.task-card :task="$task" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 