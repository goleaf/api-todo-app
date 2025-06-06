<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * Display a listing of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::withCount(['tasks' => function ($query) {
            $query->where('completed', false);
        }])
        ->latest()
        ->paginate(10);

        return $this->successResponse($categories);
    }

    /**
     * Store a newly created category.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->successResponse($category, 'Category created successfully');
    }

    /**
     * Display the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        $tasks = $category->tasks()
            ->with(['user', 'tags'])
            ->latest()
            ->paginate(10);

        return $this->successResponse([
            'category' => $category,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Update the specified category.
     *
     * @param  \App\Http\Requests\CategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->successResponse($category, 'Category updated successfully');
    }

    /**
     * Remove the specified category.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully');
    }

    /**
     * Get categories by user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function userCategories(User $user)
    {
        $categories = Category::where('user_id', $user->id)
            ->withCount(['tasks' => function ($query) {
                $query->where('completed', false);
            }])
            ->latest()
            ->paginate(10);

        return $this->successResponse($categories);
    }
} 