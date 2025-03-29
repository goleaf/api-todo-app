<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">All Tasks</h5>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                                <i class="bi bi-plus-lg me-1"></i> Add New Task
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search bar -->
                        <div class="row mb-4">
                            <div class="col-md-12">
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
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover" id="tasksTable">
                                <thead>
                                    <tr>
                                        <th width="40"></th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(auth()->user()->tasks()->orderBy('priority', 'desc')->latest()->get() as $task)
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
                                                @if($task->description)
                                                    <button class="btn btn-sm text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#desc-{{ $task->id }}" aria-expanded="false">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                    <div class="collapse mt-2" id="desc-{{ $task->id }}">
                                                        <div class="card card-body bg-light">
                                                            {{ $task->description }}
                                                        </div>
                                                    </div>
                                                @endif
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
                                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editTaskModal" 
                                                    onclick="prepareEditModal({{ $task->id }}, '{{ $task->title }}', '{{ $task->description }}', '{{ $task->status }}', {{ $task->priority }}, '{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDeleteTask({{ $task->id }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="noTasksRow">
                                            <td colspan="6" class="text-center py-5">
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
                            <div id="noSearchResults" class="text-center py-5 d-none">
                                <div class="text-muted">
                                    <i class="bi bi-search fs-4 d-block mb-3"></i>
                                    <p class="mb-0">No matching tasks found</p>
                                    <p class="small">Try a different search term or create a new task.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTaskModalLabel">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add Task Form -->
                    <form id="addTaskForm" action="{{ route('tasks.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="0">Low</option>
                                    <option value="1">Medium</option>
                                    <option value="2">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="completed" name="completed" value="1">
                            <label class="form-check-label" for="completed">Mark as completed</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('addTaskForm').submit()">Save Task</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Edit Task Form -->
                    <form id="editTaskForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_status" class="form-label">Status</label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_priority" class="form-label">Priority</label>
                                <select class="form-select" id="edit_priority" name="priority">
                                    <option value="0">Low</option>
                                    <option value="1">Medium</option>
                                    <option value="2">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="edit_due_date" name="due_date">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="edit_completed" name="completed" value="1">
                            <label class="form-check-label" for="edit_completed">Mark as completed</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('editTaskForm').submit()">Update Task</button>
                </div>
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
    
        function prepareEditModal(id, title, description, status, priority, dueDate) {
            const form = document.getElementById('editTaskForm');
            form.action = `/tasks/${id}`;
            
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_priority').value = priority;
            document.getElementById('edit_due_date').value = dueDate;
            document.getElementById('edit_completed').checked = status === 'completed';
        }
        
        function confirmDeleteTask(id) {
            if (confirm('Are you sure you want to delete this task?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/tasks/${id}`;
                const csrfField = document.createElement('input');
                csrfField.type = 'hidden';
                csrfField.name = '_token';
                csrfField.value = document.querySelector('meta[name="csrf-token"]').content;
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfField);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
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