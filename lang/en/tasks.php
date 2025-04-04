<?php

return [
    'title' => 'Tasks',
    'create' => 'Create Task',
    'edit' => 'Edit Task',
    'new' => 'New Task',
    'fields' => [
        'title' => 'Title',
        'description' => 'Description',
        'category' => 'Category',
        'due_date' => 'Due Date',
        'priority' => 'Priority',
        'status' => 'Status',
        'tags' => 'Tags',
    ],
    'priority' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ],
    'status' => [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ],
    'actions' => [
        'create' => 'Create Task',
        'update' => 'Update Task',
        'delete' => 'Delete Task',
        'cancel' => 'Cancel',
    ],
    'messages' => [
        'created' => 'Task created successfully.',
        'updated' => 'Task updated successfully.',
        'deleted' => 'Task deleted successfully.',
        'none_found' => 'No tasks found. Create your first task!',
        'confirm_delete' => 'Are you sure you want to delete this task?',
    ],
]; 