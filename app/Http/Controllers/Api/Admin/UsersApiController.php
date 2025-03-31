<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UsersApiController extends Controller
{
    /**
     * Display a listing of users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();
        
        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->has('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('active', true);
            } elseif ($status === 'inactive') {
                $query->where('active', false);
            }
        }
        
        // Sort users
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $allowedSortFields = ['id', 'name', 'email', 'created_at'];
        
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
        }
        
        // Include task counts
        $query->withCount('tasks');
        
        // Paginate results
        $perPage = $request->input('per_page', 15);
        $users = $query->fastPaginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    
    /**
     * Display the specified user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['tasks' => function($query) {
            $query->latest()->limit(10);
        }, 'categories', 'tags']);
        
        $user->loadCount(['tasks', 'categories', 'tags']);
        
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
    
    /**
     * Store a newly created user.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'active' => $request->has('active') ? $request->active : true
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }
    
    /**
     * Update the specified user.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => [
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|string|min:8',
            'active' => 'boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        if ($request->has('active')) {
            $user->active = $request->active;
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }
    
    /**
     * Remove the specified user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        // Check if user has tasks, categories, or tags
        $hasRelatedData = $user->tasks()->count() > 0 || 
                         $user->categories()->count() > 0 || 
                         $user->tags()->count() > 0;
        
        if ($hasRelatedData) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with related data. Please delete or reassign their tasks, categories, and tags first.'
            ], 422);
        }
        
        $user->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
    
    /**
     * Toggle user active status.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function toggleActive(User $user): JsonResponse
    {
        $user->active = !$user->active;
        $user->save();
        
        $status = $user->active ? 'activated' : 'deactivated';
        
        return response()->json([
            'success' => true,
            'message' => "User {$status} successfully",
            'data' => $user
        ]);
    }
    
    /**
     * Get user statistics.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function statistics(User $user): JsonResponse
    {
        $stats = [
            'task_counts' => [
                'total' => $user->tasks()->count(),
                'completed' => $user->tasks()->where('completed', true)->count(),
                'incomplete' => $user->tasks()->where('completed', false)->count(),
                'overdue' => $user->tasks()
                    ->where('completed', false)
                    ->where('due_date', '<', now())
                    ->count()
            ],
            'category_count' => $user->categories()->count(),
            'tag_count' => $user->tags()->count(),
            'recent_activity' => $user->tasks()
                ->with(['category', 'tags'])
                ->latest()
                ->limit(5)
                ->get()
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
} 