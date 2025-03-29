<?php

namespace App\Http\Controllers;

use App\Events\TodoCreated;
use App\Events\TodoDeleted;
use App\Events\TodoUpdated;
use App\Http\Requests\Todo\StoreTodoRequest;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = auth()->user()->todos()->with('category');

        // Filter by category
        if (request()->has('category')) {
            $query->where('category_id', request()->category);
        }

        return $query->latest()->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request): JsonResponse
    {
        $todo = $request->user()->todos()->create($request->validated());

        // Load the relationship
        $todo->load('category');

        broadcast(new TodoCreated($todo))->toOthers();

        return response()->json($todo, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        $this->authorize('view', $todo);

        return $todo;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Todo $todo)
    {
        $this->authorize('update', $todo);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
            'reminder_at' => 'nullable|date',
            'priority' => 'nullable|integer|in:0,1,2',
            'progress' => 'nullable|integer|min:0|max:100',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // If we're marking as completed, set progress to 100%
        if ($request->has('completed') && $request->completed) {
            $request->merge(['progress' => 100]);
        }

        // If progress is 100%, also mark as completed
        if ($request->has('progress') && $request->progress == 100) {
            $request->merge(['completed' => true]);
        }

        $todo->update($request->only([
            'title',
            'description',
            'completed',
            'due_date',
            'reminder_at',
            'priority',
            'progress',
            'category_id',
        ]));

        // Load the relationship
        $todo->load('category');

        broadcast(new TodoUpdated($todo))->toOthers();

        return $todo;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo): JsonResponse
    {
        $this->authorize('delete', $todo);

        $todo->delete();

        broadcast(new TodoDeleted($todo))->toOthers();

        return response()->json(null, 204);
    }
}
