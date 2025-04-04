<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TagInput extends Component
{
    public $name;
    public $selected;
    public $placeholder;

    /**
     * Create a new component instance.
     *
     * @param  string  $name
     * @param  array  $selected
     * @param  string  $placeholder
     * @return void
     */
    public function __construct($name = 'tags', $selected = [], $placeholder = 'Search or create tags...')
    {
        $this->name = $name;
        
        // Format selected tags to ensure they have id and name
        $this->selected = collect($selected)->map(function ($tag) {
            if (is_object($tag)) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name
                ];
            } elseif (is_array($tag) && isset($tag['id']) && isset($tag['name'])) {
                return $tag;
            } else {
                return [
                    'id' => $tag,
                    'name' => $tag
                ];
            }
        })->toArray();
        
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.tag-input');
    }
} 