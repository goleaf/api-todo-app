<?php

namespace App\Traits;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    /**
     * Get all comments for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get only published comments for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function publishedComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->published();
    }

    /**
     * Get only pending comments for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function pendingComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->pending();
    }

    /**
     * Get only spam comments for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function spamComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->spam();
    }

    /**
     * Add a comment to this model.
     *
     * @param string $content
     * @param int $userId
     * @param int|null $parentId
     * @param string $status
     * @return \App\Models\Comment
     */
    public function addComment(string $content, int $userId, int $parentId = null, string $status = 'published'): Comment
    {
        return $this->comments()->create([
            'content' => $content,
            'user_id' => $userId,
            'parent_id' => $parentId,
            'status' => $status,
        ]);
    }

    /**
     * Count comments for this model.
     *
     * @return int
     */
    public function commentCount(): int
    {
        return $this->comments()->count();
    }

    /**
     * Count published comments for this model.
     *
     * @return int
     */
    public function publishedCommentCount(): int
    {
        return $this->publishedComments()->count();
    }

    /**
     * Get root-level comments (comments without parents).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function rootComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->whereNull('parent_id');
    }

    /**
     * Get published root-level comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function publishedRootComments(): MorphMany
    {
        return $this->rootComments()->published();
    }
} 