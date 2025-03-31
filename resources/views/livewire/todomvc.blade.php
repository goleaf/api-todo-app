<div class="min-h-screen bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-4">TodoMVC</h1>
                    <p class="mb-6 text-gray-600">A Livewire implementation of the classic <a href="http://todomvc.com" class="text-blue-500 hover:underline" target="_blank">TodoMVC</a> reference application.</p>
                    
                    <section class="todoapp bg-white rounded-lg shadow-md border border-gray-200">
                        <header class="header p-4 border-b border-gray-200">
                            <form wire:submit.prevent="addTodo" class="flex items-center">
                                <input class="new-todo w-full p-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                    wire:model.lazy="newTodo"
                                    placeholder="What needs to be done?" 
                                    autofocus>
                                <button type="submit" class="p-3 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700">
                                    Add
                                </button>
                            </form>
                        </header>
                        
                        <!-- This section is displayed when there are tasks -->
                        @if($tasks->count() > 0)
                        <section class="main p-4">
                            <ul class="todo-list divide-y divide-gray-200">
                                @foreach($tasks as $task)
                                <li class="{{ $task->completed ? 'completed' : '' }} {{ $editing === $task->id ? 'editing' : '' }} flex items-center p-3" wire:key="task-{{ $task->id }}">
                                    <div class="view flex-1 flex items-center {{ $editing === $task->id ? 'hidden' : '' }}">
                                        <input class="toggle form-checkbox h-5 w-5 text-indigo-600 rounded" 
                                            type="checkbox" 
                                            wire:click="toggleComplete({{ $task->id }})"
                                            {{ $task->completed ? 'checked' : '' }}>
                                        <label class="mx-3 flex-1 {{ $task->completed ? 'line-through text-gray-400' : '' }}" 
                                            wire:dblclick="startEditing({{ $task->id }})">
                                            {{ $task->title }}
                                        </label>
                                        <button class="destroy text-red-500 hover:text-red-700" wire:click="deleteTodo({{ $task->id }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                    @if($editing === $task->id)
                                    <form wire:submit.prevent="updateEditing" class="flex-1 flex items-center">
                                        <input class="edit w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                            wire:model.lazy="editedTodo"
                                            wire:keydown.escape="cancelEditing">
                                        <button type="submit" class="p-2 bg-green-500 text-white rounded-r ml-2">
                                            Save
                                        </button>
                                        <button type="button" class="p-2 bg-gray-500 text-white rounded ml-2" wire:click="cancelEditing">
                                            Cancel
                                        </button>
                                    </form>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </section>
                        
                        <footer class="footer p-4 bg-gray-50 flex justify-between items-center flex-wrap">
                            <span class="todo-count text-gray-600">
                                <strong>{{ $remaining }}</strong> {{ Str::plural('item', $remaining) }} left
                            </span>
                            
                            <ul class="filters flex space-x-2">
                                <li>
                                    <a href="{{ route('todomvc.filter', ['filter' => 'all']) }}" 
                                       class="{{ $filter === 'all' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                        All
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('todomvc.filter', ['filter' => 'active']) }}"
                                       class="{{ $filter === 'active' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                        Active
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('todomvc.filter', ['filter' => 'completed']) }}"
                                       class="{{ $filter === 'completed' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                        Completed
                                    </a>
                                </li>
                                <li>|</li>
                                <li>
                                    <a href="{{ route('todomvc.filter', ['filter' => 'due-today']) }}"
                                       class="{{ $filter === 'due-today' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                        Due Today
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('todomvc.filter', ['filter' => 'overdue']) }}"
                                       class="{{ $filter === 'overdue' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                        Overdue
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('todomvc.filter', ['filter' => 'upcoming']) }}"
                                       class="{{ $filter === 'upcoming' ? 'border-b-2 border-indigo-500 text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                                        Upcoming
                                    </a>
                                </li>
                            </ul>
                            
                            @if($anyComplete)
                            <button class="clear-completed text-red-500 hover:text-red-700" wire:click="clearCompleted">
                                Clear completed
                            </button>
                            @endif
                            
                            @if($tasks->count() > 0 && $remaining > 0)
                            <button class="mark-all-complete text-green-500 hover:text-green-700 mt-2 w-full" wire:click="markAllComplete">
                                Mark all as complete
                            </button>
                            @endif
                        </footer>
                        @else
                        <div class="p-8 text-center text-gray-500">
                            <p>No tasks yet. Add your first one above!</p>
                        </div>
                        @endif
                    </section>
                    
                    <div class="mt-8 bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-lg font-semibold mb-2">About TodoMVC</h2>
                        <p>This is a Laravel Livewire implementation of the classic <a href="http://todomvc.com" class="text-blue-500 hover:underline" target="_blank">TodoMVC</a> project.</p>
                        <p class="mt-2">It demonstrates how to create a simple task management application with features like:</p>
                        <ul class="list-disc ml-5 mt-2">
                            <li>Adding new tasks</li>
                            <li>Editing existing tasks</li>
                            <li>Marking tasks as complete</li>
                            <li>Filtering tasks by status</li>
                            <li>Clearing completed tasks</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 