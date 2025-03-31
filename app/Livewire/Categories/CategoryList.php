<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class CategoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showDeleteModal = false;
    public $categoryToDelete = null;
    
    protected $listeners = [
        'categoryCreated' => '$refresh',
        'categoryUpdated' => '$refresh'
    ];

    public function render()
    {
        $query = Category::forUser(Auth::id());
        
        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }
        
        $categories = $query->orderBy($this->sortField, $this->sortDirection)
                          ->paginate(10);
        
        return view('livewire.categories.category-list', [
            'categories' => $categories
        ]);
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    public function confirmDelete($categoryId)
    {
        $this->showDeleteModal = true;
        $this->categoryToDelete = $categoryId;
    }
    
    public function deleteCategory()
    {
        // Check if there are tasks associated with this category
        $category = Category::findOrFail($this->categoryToDelete);
        $hasRelatedTasks = $category->tasks()->count() > 0;
        
        if ($hasRelatedTasks) {
            session()->flash('error', 'Cannot delete category because it has related tasks. Please reassign or delete the tasks first.');
        } else {
            $category->delete();
            session()->flash('success', 'Category deleted successfully!');
        }
        
        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }
    
    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }
    
    public function getTaskCountProperty()
    {
        if ($this->categoryToDelete) {
            $category = Category::find($this->categoryToDelete);
            if ($category) {
                return $category->tasks()->count();
            }
        }
        
        return 0;
    }
}
