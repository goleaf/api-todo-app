<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryType;
use App\Http\Requests\Admin\CategoryStoreRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CategoryController extends AdminController
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::query()->with('user');
        
        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }
        
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }
        
        // Apply sorting using the Sortable trait
        if ($request->has('sort') || $request->has('direction')) {
            $query = $query->sortable($request->only(['sort', 'direction']));
        } else {
            // Default sorting if no sort parameters
            $query->latest();
        }
        
        $categories = $query->fastPaginate(10);
        $users = User::all();
        
        return view('admin.categories.index', compact('categories', 'users'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $users = User::all();
        $categoryTypes = CategoryType::cases();
        $isEdit = false;
        
        return view('admin.categories.form', compact('users', 'categoryTypes', 'isEdit'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        Category::create($request->validated());
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load(['user', 'tasks']);
        
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $users = User::all();
        $categoryTypes = CategoryType::cases();
        $isEdit = true;
        
        return view('admin.categories.form', compact('category', 'users', 'categoryTypes', 'isEdit'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->update($request->validated());
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if there are associated tasks
        if ($category->tasks()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with associated tasks.');
        }
        
        $category->delete();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
} 