<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
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
    ];
    
    /**
     * Get the user that owns the tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the tasks for the tag.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class);
    }
    
    /**
     * Scope a query to only include tags for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Get the number of pending tasks with this tag.
     */
    public function getPendingTasksCountAttribute(): int
    {
        return $this->tasks()->where('completed', false)->count();
    }
    
    /**
     * Get the number of completed tasks with this tag.
     */
    public function getCompletedTasksCountAttribute(): int
    {
        return $this->tasks()->where('completed', true)->count();
    }
    
    /**
     * Get the total number of tasks with this tag.
     */
    public function getTotalTasksCountAttribute(): int
    {
        return $this->tasks()->count();
    }
}
