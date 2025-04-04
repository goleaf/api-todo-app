<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TaskFilters extends Component
{
    public $categories;
    public $selectedCategory;
    public $selectedStatus;
    public $selectedPriority;

    public function __construct($categories, $selectedCategory = null, $selectedStatus = null, $selectedPriority = null)
    {
        $this->categories = $categories;
        $this->selectedCategory = $selectedCategory;
        $this->selectedStatus = $selectedStatus;
        $this->selectedPriority = $selectedPriority;
    }

    public function render()
    {
        return view('components.task-filters');
    }
} 