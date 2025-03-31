<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CategoryCreate extends Component
{
    public $name = '';
    public $description = '';
    public $color = '#3b82f6'; // Default blue color
    public $icon = '';
    
    protected $rules = [
        'name' => 'required|string|min:2|max:255',
        'description' => 'nullable|string|max:1000',
        'color' => 'required|string|max:50',
        'icon' => 'nullable|string|max:50',
    ];
    
    public function render()
    {
        $icons = [
            'tag', 'folder', 'book', 'briefcase', 'star', 'heart', 'flag',
            'home', 'user', 'cog', 'file', 'calendar', 'shopping-cart',
            'gift', 'money-bill', 'plane', 'car', 'graduation-cap', 'utensils',
        ];
        
        return view('livewire.categories.category-create', [
            'icons' => $icons
        ]);
    }
    
    public function create()
    {
        $this->validate();
        
        // Check if user already has a category with this name
        $exists = Category::where('user_id', Auth::id())
                         ->where('name', $this->name)
                         ->exists();
        
        if ($exists) {
            $this->addError('name', 'You already have a category with this name.');
            return;
        }
        
        // Create the category
        Category::create([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'user_id' => Auth::id(),
        ]);
        
        session()->flash('success', 'Category created successfully!');
        $this->dispatch('categoryCreated');
        
        // Reset form
        $this->reset(['name', 'description', 'icon']);
        $this->color = '#3b82f6';
        
        // Redirect to categories list
        return redirect()->route('categories.index');
    }
    
    public function cancel()
    {
        return redirect()->route('categories.index');
    }
}
