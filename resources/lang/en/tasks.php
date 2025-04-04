<?php

return [
    // Task attributes
    'title' => 'Title',
    'description' => 'Description',
    'due_date' => 'Due Date',
    'status' => [
        'label' => 'Status',
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],
    'priority' => [
        'label' => 'Priority',
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ],
    'category' => 'Category',
    'tags' => 'Tags',
    'attachments' => 'Attachments',
    
    // Task state descriptions
    'due' => 'Due',
    'overdue' => 'Overdue',
    'no_due_date' => 'No due date',
    
    // CRUD operations
    'create' => 'Create Task',
    'edit' => 'Edit Task',
    'update' => 'Update Task',
    'delete' => 'Delete Task',
    'confirm_delete' => 'Are you sure you want to delete this task?',
    
    // Task list
    'all_tasks' => 'All Tasks',
    'my_tasks' => 'My Tasks',
    'due_today' => 'Due Today',
    'due_this_week' => 'Due This Week',
    'completed_tasks' => 'Completed Tasks',
    'no_tasks' => 'No tasks found',
    'filter' => 'Filter',
    'search' => 'Search tasks...',
    
    // Task details
    'task_details' => 'Task Details',
    'created_at' => 'Created at',
    'updated_at' => 'Updated at',
    'completed_at' => 'Completed at',
    'time_tracking' => 'Time Tracking',
    'time_spent' => 'Time Spent',
    'time_estimated' => 'Time Estimated',
    
    // Success messages
    'created' => 'Task created successfully!',
    'updated' => 'Task updated successfully!',
    'deleted' => 'Task deleted successfully!',
    'completed' => 'Task marked as completed!',
    
    // Error messages
    'not_found' => 'Task not found',
    'create_error' => 'Error creating task',
    'update_error' => 'Error updating task',
    'delete_error' => 'Error deleting task',
]; 