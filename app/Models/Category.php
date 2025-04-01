<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'color',
        'icon',
    ];

    /**
     * Get the user that owns the category.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks for the category.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Scope a query to only include categories for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the number of pending tasks in this category.
     */
    public function getPendingTasksCountAttribute(): int
    {
        return $this->tasks()->where('completed', false)->count();
    }

    /**
     * Get the number of completed tasks in this category.
     */
    public function getCompletedTasksCountAttribute(): int
    {
        return $this->tasks()->where('completed', true)->count();
    }

    /**
     * Get the total number of tasks in this category.
     */
    public function getTotalTasksCountAttribute(): int
    {
        return $this->tasks()->count();
    }
}
