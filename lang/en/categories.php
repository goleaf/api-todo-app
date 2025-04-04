<?php

return [
    'title' => 'Categories',
    'create' => 'Create Category',
    'edit' => 'Edit Category',
    'new' => 'New Category',
    'fields' => [
        'name' => 'Name',
        'description' => 'Description',
        'color' => 'Color',
    ],
    'actions' => [
        'create' => 'Create Category',
        'update' => 'Update Category',
        'delete' => 'Delete Category',
        'cancel' => 'Cancel',
    ],
    'messages' => [
        'created' => 'Category created successfully.',
        'updated' => 'Category updated successfully.',
        'deleted' => 'Category deleted successfully.',
        'none_found' => 'No categories found. Create your first category!',
        'confirm_delete' => 'Are you sure you want to delete this category?',
        'has_tasks' => '{0} No tasks|{1} 1 task|[2,*] :count tasks',
    ],
]; 