<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CategoryEdit extends Component
{
    public Category $category;
    
    public $name = '';
    public $description = '';
    public $color = '';
    public $icon = '';
    
    protected $rules = [
        'name' => 'required|string|min:2|max:255',
        'description' => 'nullable|string|max:1000',
        'color' => 'required|string|max:50',
        'icon' => 'nullable|string|max:50',
    ];
    
    public function mount(Category $category)
    {
        // Authorization check
        if ($category->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to edit this category.');
        }
        
        $this->category = $category;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->color = $category->color;
        $this->icon = $category->icon;
    }
    
    public function render()
    {
        $icons = [
            'tag', 'folder', 'book', 'briefcase', 'star', 'heart', 'flag',
            'home', 'user', 'cog', 'file', 'calendar', 'shopping-cart',
            'gift', 'money-bill', 'plane', 'car', 'graduation-cap', 'utensils',
        ];
        
        return view('livewire.categories.category-edit', [
            'icons' => $icons
        ]);
    }
    
    public function update()
    {
        $this->validate();
        
        // Check if user already has another category with this name
        $exists = Category::where('user_id', Auth::id())
                         ->where('name', $this->name)
                         ->where('id', '!=', $this->category->id)
                         ->exists();
        
        if ($exists) {
            $this->addError('name', 'You already have another category with this name.');
            return;
        }
        
        $this->category->update([
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
        ]);
        
        session()->flash('success', 'Category updated successfully!');
        $this->dispatch('categoryUpdated');
        
        return redirect()->route('categories.index');
    }
    
    public function cancel()
    {
        return redirect()->route('categories.index');
    }
}
