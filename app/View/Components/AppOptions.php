<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppOptions extends Component
{
    /**
     * The option key.
     *
     * @var string
     */
    public $key;

    /**
     * The default value.
     *
     * @var mixed
     */
    public $default;

    /**
     * The option value.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new component instance.
     *
     * @param string $key
     * @param mixed $default
     * @return void
     */
    public function __construct($key, $default = null)
    {
        $this->key = $key;
        $this->default = $default;
        $this->value = option($key, $default);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.app-options');
    }
} 