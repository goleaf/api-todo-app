<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends BaseController
{
    /**
     * Display a listing of categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())
            ->withCount(['tasks' => function ($query) {
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
        $category = new Category($request->validated());
        $category->user_id = Auth::id();
        $category->save();

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
        $this->authorize('view', $category);

        $tasks = $category->tasks()
            ->where('user_id', Auth::id())
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
        $this->authorize('update', $category);

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
        $this->authorize('delete', $category);

        $category->delete();

        return $this->successResponse(null, 'Category deleted successfully');
    }
} 