<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'due_date',
        'priority',
        'completed',
        'user_id',
        'category_id',
        'tags',
        'notes',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
        'priority' => 'integer',
        'tags' => 'array',
        'attachments' => 'array',
    ];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array<int, string>
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
     * Get the category that the task belongs to.
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
     * Scope a query to only include tasks with a specific due date.
     */
    public function scopeDueOn(Builder $query, string $date): Builder
    {
        return $query->whereDate('due_date', $date);
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
            ->where('due_date', '<', Carbon::today());
    }

    /**
     * Scope a query to only include upcoming tasks.
     */
    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->where('completed', false)
            ->whereBetween('due_date', [
                Carbon::today(),
                Carbon::today()->addDays($days),
            ]);
    }

    /**
     * Scope a query to only include tasks with a specific priority.
     */
    public function scopeWithPriority(Builder $query, int $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include tasks with specific tags.
     */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->where('tags', 'like', '%"'.$tag.'"%');
    }

    /**
     * Scope a query to only include tasks for a specific category.
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to order tasks by priority (highest first).
     */
    public function scopeOrderByPriority(Builder $query): Builder
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Scope a query to order tasks by due date (closest first).
     */
    public function scopeOrderByDueDate(Builder $query): Builder
    {
        return $query->orderBy('due_date');
    }

    /**
     * Scope a query to search tasks by keyword.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    /**
     * Toggle the completed status of the task.
     */
    public function toggleCompletion(): bool
    {
        $this->completed = ! $this->completed;

        return $this->save();
    }

    /**
     * Determine if the task is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->completed) {
            return false;
        }

        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Determine if the task is due today.
     */
    public function isDueToday(): bool
    {
        return $this->due_date && $this->due_date->isToday();
    }

    /**
     * Determine if the task is upcoming (due within the next few days).
     */
    public function isUpcoming(int $days = 7): bool
    {
        if (! $this->due_date || $this->completed) {
            return false;
        }

        return $this->due_date->isFuture() &&
               $this->due_date->lte(Carbon::today()->addDays($days));
    }

    /**
     * Get the formatted due date.
     */
    public function getFormattedDueDateAttribute(): ?string
    {
        return $this->due_date ? $this->due_date->format('Y-m-d') : null;
    }

    /**
     * Get the priority label.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            1 => 'Low',
            2 => 'Medium',
            3 => 'High',
            4 => 'Urgent',
            default => 'None',
        };
    }
}
