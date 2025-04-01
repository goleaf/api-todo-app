<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    /**
     * Get all comments for a model.
     *
     * @param Model $model
     * @param int $perPage
     * @param string $orderBy
     * @param string $direction
     * @return LengthAwarePaginator
     */
    public function getComments(
        Model $model,
        int $perPage = 10,
        string $orderBy = 'created_at',
        string $direction = 'desc'
    ): LengthAwarePaginator {
        return $model->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderBy($orderBy, $direction)
            ->fastPaginate($perPage);
    }
    
    /**
     * Get published comments for a model.
     *
     * @param Model $model
     * @param int $perPage
     * @param string $orderBy
     * @param string $direction
     * @return LengthAwarePaginator
     */
    public function getPublishedComments(
        Model $model,
        int $perPage = 10,
        string $orderBy = 'created_at',
        string $direction = 'desc'
    ): LengthAwarePaginator {
        return $model->publishedComments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderBy($orderBy, $direction)
            ->fastPaginate($perPage);
    }
    
    /**
     * Create a new comment for a model.
     *
     * @param Model $model
     * @param string $content
     * @param int|null $userId
     * @param int|null $parentId
     * @param string $status
     * @return Comment
     */
    public function createComment(
        Model $model,
        string $content,
        ?int $userId = null,
        ?int $parentId = null,
        string $status = 'published'
    ): Comment {
        $userId = $userId ?? Auth::id();
        
        return $model->addComment($content, $userId, $parentId, $status);
    }
    
    /**
     * Update an existing comment.
     *
     * @param Comment $comment
     * @param string $content
     * @return bool
     */
    public function updateComment(Comment $comment, string $content): bool
    {
        return $comment->update(['content' => $content]);
    }
    
    /**
     * Delete a comment.
     *
     * @param Comment $comment
     * @return bool|null
     */
    public function deleteComment(Comment $comment): ?bool
    {
        return $comment->delete();
    }
    
    /**
     * Change comment status.
     *
     * @param Comment $comment
     * @param string $status
     * @return bool
     */
    public function changeStatus(Comment $comment, string $status): bool
    {
        return $comment->update(['status' => $status]);
    }
    
    /**
     * Check if user is authorized to update a comment.
     *
     * @param Comment $comment
     * @param int|null $userId
     * @return bool
     */
    public function canUpdateComment(Comment $comment, ?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        
        // User can update their own comments
        if ($comment->user_id === $userId) {
            return true;
        }
        
        // Admin/moderator can update any comment
        if (Auth::user() && Auth::user()->isAdmin()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if user is authorized to delete a comment.
     *
     * @param Comment $comment
     * @param int|null $userId
     * @return bool
     */
    public function canDeleteComment(Comment $comment, ?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        
        // User can delete their own comments
        if ($comment->user_id === $userId) {
            return true;
        }
        
        // Admin/moderator can delete any comment
        if (Auth::user() && Auth::user()->isAdmin()) {
            return true;
        }
        
        // Content owner can delete comments on their content
        $commentable = $comment->commentable;
        if ($commentable && method_exists($commentable, 'user_id')) {
            return $commentable->user_id === $userId;
        }
        
        return false;
    }
} 