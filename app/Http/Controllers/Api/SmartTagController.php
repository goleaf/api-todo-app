<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SmartTag\StoreSmartTagRequest;
use App\Http\Requests\Api\SmartTag\UpdateSmartTagRequest;
use App\Models\SmartTag;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SmartTagController extends Controller
{
    /**
     * Display a listing of smart tags.
     */
    public function index(Request $request)
    {
        $query = SmartTag::where('user_id', auth()->id());

        // Sort options
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $allowedSortFields = ['name', 'created_at', 'last_applied_at'];

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'desc' ? 'desc' : 'asc');
        }

        $smartTags = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $smartTags->items(),
            'meta' => [
                'current_page' => $smartTags->currentPage(),
                'last_page' => $smartTags->lastPage(),
                'per_page' => $smartTags->perPage(),
                'total' => $smartTags->total()
            ]
        ]);
    }

    /**
     * Store a newly created smart tag.
     */
    public function store(StoreSmartTagRequest $request)
    {
        $smartTag = new SmartTag();
        $smartTag->user_id = auth()->id();
        $smartTag->name = $request->name;
        $smartTag->description = $request->description;
        $smartTag->conditions = $request->conditions;
        $smartTag->actions = $request->actions;
        $smartTag->is_active = $request->is_active ?? true;
        $smartTag->save();

        // Apply the smart tag to existing tasks
        $this->applySmartTag($smartTag);

        return response()->json([
            'message' => 'Smart tag created successfully',
            'data' => $smartTag
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified smart tag.
     */
    public function show(SmartTag $smartTag)
    {
        $this->authorize('view', $smartTag);

        return response()->json([
            'data' => $smartTag
        ]);
    }

    /**
     * Update the specified smart tag.
     */
    public function update(UpdateSmartTagRequest $request, SmartTag $smartTag)
    {
        $this->authorize('update', $smartTag);

        $smartTag->name = $request->name ?? $smartTag->name;
        $smartTag->description = $request->description ?? $smartTag->description;
        $smartTag->conditions = $request->conditions ?? $smartTag->conditions;
        $smartTag->actions = $request->actions ?? $smartTag->actions;
        $smartTag->is_active = $request->is_active ?? $smartTag->is_active;
        $smartTag->save();

        // Reapply the smart tag to existing tasks if conditions or actions changed
        if ($request->has('conditions') || $request->has('actions')) {
            $this->applySmartTag($smartTag);
        }

        return response()->json([
            'message' => 'Smart tag updated successfully',
            'data' => $smartTag
        ]);
    }

    /**
     * Remove the specified smart tag.
     */
    public function destroy(SmartTag $smartTag)
    {
        $this->authorize('delete', $smartTag);

        $smartTag->delete();

        return response()->json([
            'message' => 'Smart tag deleted successfully'
        ]);
    }
    
    /**
     * Get tasks matching the smart tag criteria.
     */
    public function tasks(Request $request, SmartTag $smartTag)
    {
        // Authorize the request
        $this->authorize('view', $smartTag);
        
        // Start building the task query
        $query = Task::query()->where('user_id', $request->user()->id);
        
        // Apply filter by status
        if ($smartTag->filter_by_status) {
            $query->where('completed', $smartTag->status_completed);
        }
        
        // Apply filter by priority
        if ($smartTag->filter_by_priority && $smartTag->priority_values) {
            $priorities = json_decode($smartTag->priority_values, true);
            if (!empty($priorities)) {
                $query->whereIn('priority', $priorities);
            }
        }
        
        // Apply filter by category
        if ($smartTag->filter_by_category && $smartTag->category_ids) {
            $categoryIds = json_decode($smartTag->category_ids, true);
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }
        
        // Apply filter by due date
        if ($smartTag->filter_by_due_date) {
            switch ($smartTag->due_date_operator) {
                case 'today':
                    $query->whereDate('due_date', Carbon::today());
                    break;
                    
                case 'this_week':
                    $query->whereDate('due_date', '>=', Carbon::today())
                          ->whereDate('due_date', '<=', Carbon::today()->addDays(7));
                    break;
                    
                case 'overdue':
                    $query->where('completed', false)
                          ->whereDate('due_date', '<', Carbon::today());
                    break;
                    
                case 'custom':
                    if ($smartTag->due_date_values) {
                        $dateValues = json_decode($smartTag->due_date_values, true);
                        if (isset($dateValues['from'])) {
                            $query->whereDate('due_date', '>=', Carbon::parse($dateValues['from']));
                        }
                        if (isset($dateValues['to'])) {
                            $query->whereDate('due_date', '<=', Carbon::parse($dateValues['to']));
                        }
                    }
                    break;
            }
        }
        
        // Apply custom criteria if available
        if ($smartTag->criteria) {
            $criteria = json_decode($smartTag->criteria, true);
            
            // Implement custom criteria handling based on your specific needs
            // This is just an example that can be expanded based on your requirements
            if (isset($criteria['search']) && !empty($criteria['search'])) {
                $search = $criteria['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
        }
        
        // Set order
        $sortField = $request->get('sort_by', 'due_date');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['title', 'due_date', 'priority', 'created_at'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('due_date', 'asc');
        }
        
        // Load relationships
        $query->with(['category', 'tags']);
        
        // Pagination
        $perPage = min($request->get('per_page', 15), 100); // Limit to 100 items max
        
        return response()->json([
            'success' => true,
            'smart_tag' => $smartTag,
            'data' => $query->paginate($perPage),
        ]);
    }

    /**
     * Apply the smart tag to matching tasks.
     *
     * @param SmartTag $smartTag
     * @return void
     */
    private function applySmartTag(SmartTag $smartTag)
    {
        if (!$smartTag->is_active) {
            return;
        }

        // Get tasks that match the conditions
        $query = Task::where('user_id', auth()->id());

        foreach ($smartTag->conditions as $condition) {
            $query->where(function ($q) use ($condition) {
                switch ($condition['field']) {
                    case 'title':
                        $q->where('title', $condition['operator'], $condition['value']);
                        break;
                    case 'description':
                        $q->where('description', $condition['operator'], $condition['value']);
                        break;
                    case 'due_date':
                        $q->whereDate('due_date', $condition['operator'], $condition['value']);
                        break;
                    case 'priority':
                        $q->where('priority', $condition['operator'], $condition['value']);
                        break;
                    case 'status':
                        $q->where('status', $condition['operator'], $condition['value']);
                        break;
                }
            });
        }

        $tasks = $query->get();

        // Apply actions to matching tasks
        foreach ($tasks as $task) {
            foreach ($smartTag->actions as $action) {
                switch ($action['type']) {
                    case 'add_tag':
                        $task->tags()->attach($action['tag_id']);
                        break;
                    case 'remove_tag':
                        $task->tags()->detach($action['tag_id']);
                        break;
                    case 'set_category':
                        $task->category_id = $action['category_id'];
                        $task->save();
                        break;
                    case 'set_priority':
                        $task->priority = $action['priority'];
                        $task->save();
                        break;
                    case 'set_status':
                        $task->status = $action['status'];
                        $task->save();
                        break;
                }
            }
        }

        // Update last applied timestamp
        $smartTag->last_applied_at = now();
        $smartTag->save();
    }
}
