<x-app-layout>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="bg-white shadow rounded p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0">My Tasks</h2>
                    <div class="d-flex">
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary btn-sm me-2">
                            <i class="bi bi-list-task me-1"></i> View All
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                            <i class="bi bi-plus-lg me-1"></i> Add New Task
                        </button>
                    </div>
                </div>
                
                <!-- Search bar -->
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input 
                            type="text" 
                            id="taskSearch" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="Search tasks by title or description..."
                        >
                        <button 
                            id="clearSearch" 
                            class="input-group-text bg-white border-start-0 d-none" 
                        >
                            <i class="bi bi-x-circle text-muted"></i>
                        </button>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Pending</h6>
                                        <h3 class="mb-0">{{ auth()->user()->tasks()->where('status', 'pending')->count() }}</h3>
                                    </div>
                                    <div class="rounded-circle bg-warning bg-opacity-25 p-3">
                                        <i class="bi bi-hourglass text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">In Progress</h6>
                                        <h3 class="mb-0">{{ auth()->user()->tasks()->where('status', 'in_progress')->count() }}</h3>
                                    </div>
                                    <div class="rounded-circle bg-primary bg-opacity-25 p-3">
                                        <i class="bi bi-gear-wide-connected text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-light mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1">Completed</h6>
                                        <h3 class="mb-0">{{ auth()->user()->tasks()->where('completed', true)->count() }}</h3>
                                    </div>
                                    <div class="rounded-circle bg-success bg-opacity-25 p-3">
                                        <i class="bi bi-check-lg text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="tasksTable">
                        <thead>
                            <tr>
                                <th scope="col" width="40"></th>
                                <th scope="col">Title</th>
                                <th scope="col">Priority</th>
                                <th scope="col">Status</th>
                                <th scope="col">Due Date</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(auth()->user()->tasks()->orderBy('priority', 'desc')->latest()->limit(5)->get() as $task)
                                <tr class="task-row" data-title="{{ strtolower($task->title) }}" data-description="{{ strtolower($task->description ?? '') }}">
                                    <td>
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input rounded-circle task-checkbox" 
                                                type="checkbox" 
                                                id="task-{{ $task->id }}" 
                                                {{ $task->completed ? 'checked' : '' }}
                                                onclick="toggleTaskCompletion({{ $task->id }}, this.checked)"
                                            >
                                        </div>
                                    </td>
                                    <td class="{{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}">
                                        {{ $task->title }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $task->priority_color }}">{{ $task->priority_label }}</span>
                                    </td>
                                    <td>
                                        @if($task->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($task->status == 'in_progress')
                                            <span class="badge bg-primary">In Progress</span>
                                        @else
                                            <span class="badge bg-success">Completed</span>
                                        @endif
                                    </td>
                                    <td>{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr id="noTasksRow">
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-clipboard2-x fs-4 d-block mb-3"></i>
                                            No tasks found. Click "Add New Task" to create your first task.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- No search results message (hidden by default) -->
                    <div id="noSearchResults" class="text-center py-4 d-none">
                        <div class="text-muted">
                            <i class="bi bi-search fs-4 d-block mb-3"></i>
                            <p class="mb-0">No matching tasks found</p>
                            <p class="small">Try a different search term.</p>
                        </div>
                    </div>
                </div>
                
                @if(auth()->user()->tasks()->count() > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">View All Tasks</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <style>
        .task-checkbox:checked {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        
        .input-group-text {
            cursor: pointer;
        }
        
        .input-group-text:hover {
            color: #6f42c1;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Client-side search functionality
            const searchInput = document.getElementById('taskSearch');
            const clearButton = document.getElementById('clearSearch');
            const taskRows = document.querySelectorAll('.task-row');
            const noTasksRow = document.getElementById('noTasksRow');
            const noSearchResults = document.getElementById('noSearchResults');
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                let visibleCount = 0;
                
                // Show/hide clear button
                if (searchTerm !== '') {
                    clearButton.classList.remove('d-none');
                } else {
                    clearButton.classList.add('d-none');
                }
                
                // Filter tasks
                taskRows.forEach(row => {
                    const title = row.getAttribute('data-title');
                    const description = row.getAttribute('data-description');
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        row.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        row.classList.add('d-none');
                    }
                });
                
                // Show no results message if needed
                if (visibleCount === 0 && searchTerm !== '') {
                    noSearchResults.classList.remove('d-none');
                    if (noTasksRow) noTasksRow.classList.add('d-none');
                } else {
                    noSearchResults.classList.add('d-none');
                    if (noTasksRow && taskRows.length === 0) noTasksRow.classList.remove('d-none');
                }
            });
            
            // Clear search
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                searchInput.focus();
            });
        });
        
        function toggleTaskCompletion(taskId, isCompleted) {
            fetch(`/api/tasks/${taskId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    completed: isCompleted,
                    status: isCompleted ? 'completed' : 'pending'
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Refresh the page to show updated state
                window.location.reload();
            })
            .catch(error => {
                console.error('Error updating task:', error);
                alert('Failed to update task status. Please try again.');
                // Revert checkbox state on error
                document.getElementById(`task-${taskId}`).checked = !isCompleted;
            });
        }
    </script>
</x-app-layout> 