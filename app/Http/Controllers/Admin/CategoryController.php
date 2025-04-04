<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Category::with('user');
        
        // Filter by user if specified
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        // Search by name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Order by
        $orderBy = $request->order_by ?? 'name';
        $direction = $request->direction ?? 'asc';
        $query->orderBy($orderBy, $direction);
        
        $categories = $query->paginate(15);
        $users = User::all(['id', 'name']);
        
        return view('pages.admin.categories.index', compact('categories', 'users'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $users = User::all(['id', 'name']);
        
        return view('pages.admin.categories.create', compact('users'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \App\Http\Requests\Admin\Category\StoreCategoryRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category): View
    {
        $category->load(['user', 'tasks']);
        
        return view('pages.admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category): View
    {
        $users = User::all(['id', 'name']);
        
        return view('pages.admin.categories.edit', compact('category', 'users'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \App\Http\Requests\Admin\Category\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Check if the category has associated tasks
        if ($category->tasks()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated tasks.');
        }
        
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
    
    /**
     * Display a listing of tasks belonging to the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function categoryTasks(Category $category): View
    {
        $tasks = $category->tasks()->with(['tags', 'user'])->paginate(15);
        
        return view('pages.admin.categories.tasks', compact('category', 'tasks'));
    }
}
