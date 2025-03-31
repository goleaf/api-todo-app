<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Task;
use App\Traits\ApiResponses;
use App\Traits\LogsErrors;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="API endpoints for dashboard data and statistics"
 * )
 */
class DashboardController extends Controller
{
    use ApiResponses, LogsErrors;

    /**
     * Get dashboard data for the authenticated user
     *
     * @OA\Get(
     *     path="/api/v1/dashboard",
     *     summary="Get dashboard data for the authenticated user",
     *     description="Retrieves dashboard statistics, categories, recent tasks, and upcoming deadlines",
     *     operationId="getDashboardData",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard data retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Dashboard data retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="stats",
     *                     type="object",
     *                     @OA\Property(property="total", type="integer", example=10),
     *                     @OA\Property(property="completed", type="integer", example=5),
     *                     @OA\Property(property="pending", type="integer", example=5),
     *                     @OA\Property(property="overdue", type="integer", example=2),
     *                     @OA\Property(property="completion_rate", type="integer", example=50)
     *                 ),
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Work"),
     *                         @OA\Property(property="color", type="string", example="#FF5733"),
     *                         @OA\Property(property="tasks_count", type="integer", example=5)
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="recentTasks",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Complete project report"),
     *                         @OA\Property(property="description", type="string", example="Finalize quarterly report"),
     *                         @OA\Property(property="due_date", type="string", format="date", example="2023-04-15"),
     *                         @OA\Property(property="completed", type="boolean", example=false),
     *                         @OA\Property(
     *                             property="category",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Work"),
     *                             @OA\Property(property="color", type="string", example="#FF5733")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="upcomingDeadlines",
     *                     type="array",
     *
     *                     @OA\Items(
     *                         type="object",
     *
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Complete project report"),
     *                         @OA\Property(property="description", type="string", example="Finalize quarterly report"),
     *                         @OA\Property(property="due_date", type="string", format="date", example="2023-04-15"),
     *                         @OA\Property(property="completed", type="boolean", example=false),
     *                         @OA\Property(
     *                             property="category",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Work"),
     *                             @OA\Property(property="color", type="string", example="#FF5733")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="recentActivity",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Complete project report"),
     *                         @OA\Property(property="type", type="string", example="created", enum={"created", "completed", "updated"}),
     *                         @OA\Property(property="date", type="string", format="date-time", example="2023-04-15T10:00:00Z")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="completionRateOverTime",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="date", type="string", format="date", example="2023-04-15"),
     *                         @OA\Property(property="completion_rate", type="integer", example=50)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="status_code", type="integer", example=401),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="status_code", type="integer", example=500),
     *             @OA\Property(property="message", type="string", example="Failed to retrieve dashboard data")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $userId = $user->id;
            
            // Check if we have cached data and it's not a force refresh request
            $cacheKey = 'dashboard_' . $userId;
            
            if (!$request->has('refresh') && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Get task statistics using the model's query scopes
            $totalTasks = Task::forUser($userId)->count();
            $completedTasks = Task::forUser($userId)->completed()->count();
            $pendingTasks = Task::forUser($userId)->incomplete()->count();
            $overdueTasks = Task::forUser($userId)->overdue()->count();

            // Calculate completion rate
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            // Get categories with task counts using a more efficient query
            $categories = Category::where('user_id', $userId)
                ->withCount(['tasks' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }])
                ->orderBy('tasks_count', 'desc')
                ->take(5)
                ->get();

            // Get recent tasks using model's with() relationship
            $recentTasks = Task::with('category')
                ->forUser($userId)
                ->latest()
                ->take(5)
                ->get();

            // Get upcoming deadlines using model's scopes
            $upcomingDeadlines = Task::with('category')
                ->forUser($userId)
                ->incomplete()
                ->upcoming()
                ->orderBy('due_date')
                ->take(5)
                ->get();

            // Get recent activity more efficiently
            $recentActivity = $this->getRecentActivity($userId);

            // Get completion rate over time
            $completionRateOverTime = $this->getCompletionRateOverTime($userId);

            $data = [
                'stats' => [
                    'total' => $totalTasks,
                    'completed' => $completedTasks,
                    'pending' => $pendingTasks,
                    'overdue' => $overdueTasks,
                    'completion_rate' => $completionRate,
                ],
                'categories' => $categories,
                'recentTasks' => $recentTasks,
                'upcomingDeadlines' => $upcomingDeadlines,
                'recentActivity' => $recentActivity,
                'completionRateOverTime' => $completionRateOverTime,
            ];

            $response = $this->successResponse(
                data: $data,
                message: 'Dashboard data retrieved successfully'
            );
            
            // Cache the dashboard data
            Cache::put($cacheKey, $response, now()->addMinutes(15));
            
            return $response;
        } catch (Throwable $e) {
            $this->logError($e);

            return $this->serverErrorResponse('Failed to retrieve dashboard data');
        }
    }

    /**
     * Get user's recent task activity
     */
    private function getRecentActivity(int $userId): array
    {
        // Check if we have cached activity data
        $cacheKey = 'recent_activity_' . $userId;
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // First query: recently created tasks
        $createdTasks = Task::forUser($userId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->select(['id', 'title', 'created_at'])
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'type' => 'created',
                    'date' => $task->created_at,
                ];
            });

        // Second query: recently completed tasks
        $completedTasks = Task::forUser($userId)
            ->completed()
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startOfWeek, $endOfWeek])
            ->select(['id', 'title', 'completed_at'])
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'type' => 'completed',
                    'date' => $task->completed_at,
                ];
            });

        // Third query: recently updated tasks (not creation or completion)
        $updatedTasks = Task::forUser($userId)
            ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
            ->whereColumn('created_at', '!=', 'updated_at')
            ->whereRaw('(completed_at IS NULL OR completed_at != updated_at)')
            ->select(['id', 'title', 'updated_at'])
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'type' => 'updated',
                    'date' => $task->updated_at,
                ];
            });

        // Merge all activity types and sort by date
        $recentActivity = $createdTasks->concat($completedTasks)->concat($updatedTasks)
            ->sortByDesc('date')
            ->values()
            ->take(10)
            ->toArray();
            
        // Cache the results
        Cache::put($cacheKey, $recentActivity, now()->addMinutes(60));
        
        return $recentActivity;
    }

    /**
     * Get completion rate over time
     */
    private function getCompletionRateOverTime(int $userId): array
    {
        $cacheKey = 'completion_rate_' . $userId;
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(6);
        $dateRange = [];

        // Initialize date range with zeroes
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $dateRange[$formattedDate] = [
                'date' => $formattedDate,
                'total' => 0,
                'completed' => 0,
                'completion_rate' => 0,
            ];
        }

        // Get total tasks per day (created before or on that day)
        $totalTasksQuery = DB::table('tasks')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as count'))
            ->where('user_id', $userId)
            ->where('created_at', '<=', $endDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get();

        // Apply totals to our date range
        foreach ($totalTasksQuery as $item) {
            $dateKey = $item->date;
            
            // Apply count to this date and all future dates in our range
            foreach ($dateRange as $date => $value) {
                if ($date >= $dateKey && Carbon::parse($date) <= $endDate) {
                    $dateRange[$date]['total'] += $item->count;
                }
            }
        }

        // Get completed tasks per day
        $completedTasksQuery = DB::table('tasks')
            ->select(DB::raw('DATE(completed_at) as date'), DB::raw('COUNT(id) as count'))
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(completed_at)'))
            ->get();

        // Apply completed counts
        foreach ($completedTasksQuery as $item) {
            $dateKey = $item->date;
            
            // Apply completed count to this date and all future dates in our range
            foreach ($dateRange as $date => $value) {
                if ($date >= $dateKey && Carbon::parse($date) <= $endDate) {
                    $dateRange[$date]['completed'] += $item->count;
                }
            }
        }

        // Calculate completion rates
        foreach ($dateRange as $date => $value) {
            if ($value['total'] > 0) {
                $dateRange[$date]['completion_rate'] = intval(($value['completed'] / $value['total']) * 100);
            }
        }

        // Format the output
        $result = array_values($dateRange);
        
        // Remove total and completed counts, only keep date and completion_rate
        $formattedResult = array_map(function ($item) {
            return [
                'date' => $item['date'],
                'completion_rate' => $item['completion_rate'],
            ];
        }, $result);
        
        // Cache the results
        Cache::put($cacheKey, $formattedResult, now()->addHours(6));
        
        return $formattedResult;
    }
}
