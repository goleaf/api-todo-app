<?php

namespace App\Services\Api;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskAnalyticsService
{
    /**
     * Get all comments for a user's tasks with user information.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserTaskComments(User $user): Collection
    {
        return $user->taskComments()
            ->with('user') // Eager load the comment author
            ->latest()
            ->get();
    }

    /**
     * Get all tags used across a user's tasks with their usage count.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserTaskTags(User $user): Collection
    {
        return $user->taskTags()
            ->select('tags.*', DB::raw('COUNT(task_tag.task_id) as usage_count'))
            ->groupBy('tags.id')
            ->orderByDesc('usage_count')
            ->get();
    }

    /**
     * Get task engagement metrics by counting unique commenters.
     *
     * @param Task $task
     * @return array
     */
    public function getTaskEngagementMetrics(Task $task): array
    {
        $commenters = $task->commenters()->count();
        $comments = $task->comments()->count();
        $replies = $task->commentReplies()->count();
        
        return [
            'total_comments' => $comments,
            'total_replies' => $replies,
            'unique_commenters' => $commenters,
            'engagement_score' => $this->calculateEngagementScore($comments, $replies, $commenters),
        ];
    }

    /**
     * Get all tasks from a user's categories with their comments.
     *
     * @param User $user
     * @return Collection
     */
    public function getUserCategoryTasks(User $user): Collection
    {
        return $user->categoryTasks()
            ->with(['category', 'comments'])
            ->get();
    }

    /**
     * Get all comments on tasks in a category.
     *
     * @param Category $category
     * @return Collection
     */
    public function getCategoryTaskComments(Category $category): Collection
    {
        return $category->taskComments()
            ->with(['user', 'commentable'])
            ->latest()
            ->get();
    }

    /**
     * Get all tags used in tasks within a specific category.
     *
     * @param Category $category
     * @return Collection
     */
    public function getCategoryTaskTags(Category $category): Collection
    {
        return $category->taskTags()
            ->select('tags.*', DB::raw('COUNT(task_tag.task_id) as usage_count'))
            ->groupBy('tags.id')
            ->orderByDesc('usage_count')
            ->get();
    }

    /**
     * Calculate an engagement score based on comment activity.
     *
     * @param int $comments
     * @param int $replies
     * @param int $commenters
     * @return float
     */
    private function calculateEngagementScore(int $comments, int $replies, int $commenters): float
    {
        if ($comments === 0) {
            return 0;
        }
        
        // Weight replies more heavily since they indicate ongoing conversation
        $replyWeight = 1.5;
        $commenterWeight = 2.0;
        
        // Base score is the number of comments
        $score = $comments;
        
        // Add weighted score for replies
        $score += $replies * $replyWeight;
        
        // Add weighted score for unique commenters
        $score += $commenters * $commenterWeight;
        
        return round($score, 1);
    }
} 