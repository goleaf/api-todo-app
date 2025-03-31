<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'completed',
        'completed_at',
        'priority',
        'due_date',
        'category_id',
        'session_id',
        'reminder_at',
        'tags',
        'progress',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'due_date' => 'date',
        'reminder_at' => 'datetime',
        'tags' => 'array',
        'progress' => 'integer',
    ];

    /**
     * Default attribute values
     */
    protected $attributes = [
        'completed' => false,
        'priority' => 0,
        'progress' => 0,
        'tags' => '[]',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['priority_label', 'priority_color', 'is_overdue', 'is_due_today', 'is_upcoming'];

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['category'];

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that owns the task.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to only include tasks for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('completed', true);
    }

    /**
     * Scope a query to only include incomplete tasks.
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where('completed', false);
    }

    /**
     * Scope a query to only include tasks due today.
     */
    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', Carbon::today());
    }

    /**
     * Scope a query to only include overdue tasks.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('completed', false)
            ->whereDate('due_date', '<', Carbon::today());
    }

    /**
     * Scope a query to only include upcoming tasks.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('completed', false)
            ->whereDate('due_date', '>', Carbon::today());
    }

    /**
     * Scope a query to order by priority descending.
     */
    public function scopeOrderByPriority(Builder $query): Builder
    {
        return $query->orderByDesc('priority');
    }

    /**
     * Scope a query to order by due date.
     */
    public function scopeOrderByDueDate(Builder $query): Builder
    {
        return $query->orderBy('due_date');
    }

    /**
     * Scope a query to filter tasks by category.
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to filter tasks by tag.
     */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->where('tags', 'like', '%"' . $tag . '"%');
    }

    /**
     * Scope a query to get tasks with progress less than specified value.
     */
    public function scopeWithProgressLessThan(Builder $query, int $progress): Builder
    {
        return $query->where('progress', '<', $progress);
    }

    /**
     * Scope a query to get tasks with progress greater than specified value.
     */
    public function scopeWithProgressGreaterThan(Builder $query, int $progress): Builder
    {
        return $query->where('progress', '>', $progress);
    }

    /**
     * Scope a query to get tasks with reminders set.
     */
    public function scopeWithReminders(Builder $query): Builder
    {
        return $query->whereNotNull('reminder_at');
    }

    /**
     * Mark the task as completed.
     */
    public function markAsCompleted(): self
    {
        $this->completed = true;
        $this->completed_at = Carbon::now();
        $this->progress = 100;
        $this->save();

        return $this;
    }

    /**
     * Mark the task as incomplete.
     */
    public function markAsIncomplete(): self
    {
        $this->completed = false;
        $this->completed_at = null;
        $this->save();

        return $this;
    }

    /**
     * Set the priority of the task.
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        $this->save();

        return $this;
    }

    /**
     * Update the progress of the task.
     */
    public function updateProgress(int $progress): self
    {
        $this->progress = min(100, max(0, $progress)); // Ensure progress is between 0-100
        
        // If progress is 100, mark as completed
        if ($this->progress === 100 && !$this->completed) {
            $this->markAsCompleted();
        } elseif ($this->progress < 100 && $this->completed) {
            // If progress is less than 100 but task is completed, mark as incomplete
            $this->completed = false;
            $this->completed_at = null;
        }
        
        $this->save();
        return $this;
    }

    /**
     * Add tags to the task.
     */
    public function addTags(array $tags): self
    {
        $currentTags = $this->tags ?? [];
        $this->tags = array_unique(array_merge($currentTags, $tags));
        $this->save();
        
        return $this;
    }

    /**
     * Remove tags from the task.
     */
    public function removeTags(array $tags): self
    {
        if (!$this->tags) {
            return $this;
        }
        
        $this->tags = array_values(array_diff($this->tags, $tags));
        $this->save();
        
        return $this;
    }

    /**
     * Set reminder for the task.
     */
    public function setReminder(Carbon $reminderTime): self
    {
        $this->reminder_at = $reminderTime;
        $this->save();
        
        return $this;
    }

    /**
     * Clear reminder from the task.
     */
    public function clearReminder(): self
    {
        $this->reminder_at = null;
        $this->save();
        
        return $this;
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

    /**
     * Check if the task is overdue.
     */
    public function getIsOverdueAttribute(): bool
    {
        return !$this->completed && 
               $this->due_date && 
               Carbon::parse($this->due_date)->startOfDay()->isPast() && 
               !Carbon::parse($this->due_date)->isToday();
    }
    
    /**
     * Check if the task is due today.
     */
    public function getIsDueTodayAttribute(): bool
    {
        return !$this->completed && 
               $this->due_date && 
               Carbon::parse($this->due_date)->isToday();
    }
    
    /**
     * Check if the task is upcoming.
     */
    public function getIsUpcomingAttribute(): bool
    {
        return !$this->completed && 
               $this->due_date && 
               Carbon::parse($this->due_date)->startOfDay()->isFuture();
    }
    
    /**
     * Check if the task needs a reminder.
     */
    public function getNeedsReminderAttribute(): bool
    {
        return $this->reminder_at && 
               !$this->completed && 
               $this->reminder_at->isPast() && 
               $this->reminder_at->diffInHours(now()) < 24;
    }
}
