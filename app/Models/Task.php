<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'completed',
        'completed_at',
        'priority',
        'progress',
        'category_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'priority' => 'integer',
        'progress' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['priority_label', 'priority_color'];

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the task belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Mark the task as complete.
     */
    public function markAsComplete()
    {
        $this->completed = true;
        $this->completed_at = now();
        $this->progress = 100;
        $this->save();
        
        return $this;
    }

    /**
     * Mark the task as incomplete.
     */
    public function markAsIncomplete()
    {
        $this->completed = false;
        $this->completed_at = null;
        $this->save();
        
        return $this;
    }

    /**
     * Scope a query to only include tasks for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include tasks for a specific category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope a query to only include incomplete tasks.
     */
    public function scopeIncomplete($query)
    {
        return $query->where('completed', false);
    }

    /**
     * Scope a query to only include tasks due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', now()->format('Y-m-d'));
    }

    /**
     * Scope a query to only include overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->whereDate('due_date', '<', now()->format('Y-m-d'))
                     ->where('completed', false);
    }

    /**
     * Scope a query to only include tasks due within the next week.
     */
    public function scopeDueThisWeek($query)
    {
        return $query->whereDate('due_date', '>=', now()->format('Y-m-d'))
                     ->whereDate('due_date', '<=', now()->addDays(7)->format('Y-m-d'));
    }

    /**
     * Scope a query to filter tasks by priority.
     */
    public function scopeWithPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to search tasks by title or description.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Get the priority label.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            0 => 'Low',
            1 => 'Medium',
            2 => 'High',
            default => 'Low',
        };
    }

    /**
     * Get the priority color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            0 => 'success',
            1 => 'warning',
            2 => 'danger',
            default => 'success',
        };
    }
}
