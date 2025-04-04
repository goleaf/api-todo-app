<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TaskPriority extends Component
{
    public $priority;
    public $color;
    public $label;
    public $icon;

    public function __construct($priority)
    {
        $this->priority = $priority;
        $this->setPriorityInfo();
    }

    private function setPriorityInfo()
    {
        $info = [
            'low' => [
                'color' => 'text-green-600',
                'label' => __('Low'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
            ],
            'medium' => [
                'color' => 'text-yellow-600',
                'label' => __('Medium'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />'
            ],
            'high' => [
                'color' => 'text-red-600',
                'label' => __('High'),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'
            ]
        ];

        $this->color = $info[$this->priority]['color'];
        $this->label = $info[$this->priority]['label'];
        $this->icon = $info[$this->priority]['icon'];
    }

    public function render()
    {
        return view('components.task-priority');
    }
} 