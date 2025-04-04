<?php

namespace App\View\Components;

use App\Services\DueDateService;
use Illuminate\View\Component;

class DueDate extends Component
{
    public $date;
    public $color;
    public $label;
    public $formattedDate;

    public function __construct($date)
    {
        $this->date = $date;
        $this->setDueDateInfo();
    }

    private function setDueDateInfo()
    {
        $service = new DueDateService();
        $info = $service->getDueDateInfo($this->date);
        
        $this->color = $info['color'];
        $this->label = $info['label'];
        $this->formattedDate = $info['formatted_date'];
    }

    public function render()
    {
        return view('components.due-date');
    }
} 