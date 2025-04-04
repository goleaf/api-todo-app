<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'category_id',
        'due_date',
        'priority',
        'status',
        'completed',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Priority levels.
     */
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    /**
     * Task statuses.
     */
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($task) {
            $task->status = $task->status ?? self::STATUS_PENDING;
        });
    }

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
     * Get the tags for the task.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag')
            ->withTimestamps();
    }

    /**
     * Get the time entries for the task.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    /**
     * Get the attachments for the task.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Scope a query to only include tasks for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include pending tasks.
     */
    public function scopePending($query)
    {
        return $query->where('completed', false);
    }

    /**
     * Scope a query to only include overdue tasks.
     */
    public function scopeOverdue($query)
    {
        return $query->where('completed', false)
            ->where('due_date', '<', now());
    }

    /**
     * Scope a query to only include upcoming tasks.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('completed', false)
            ->where('due_date', '>', now());
    }

    /**
     * Toggle the completion status of the task.
     */
    public function toggle(): bool
    {
        $this->completed = !$this->completed;
        $this->completed_at = $this->completed ? now() : null;
        return $this->save();
    }

    /**
     * Scope a query to only include tasks due today.
     */
    public function scopeDueToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', Carbon::today());
    }

    /**
     * Scope a query to only include tasks due within the next week.
     */
    public function scopeDueThisWeek(Builder $query): Builder
    {
        return $query->where('completed', false)
                     ->whereDate('due_date', '>=', Carbon::today())
                     ->whereDate('due_date', '<=', Carbon::today()->addDays(7));
    }

    /**
     * Scope a query to get high priority tasks.
     */
    public function scopeHighPriority(Builder $query): Builder
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    /**
     * Calculate the total time spent on this task.
     */
    public function getTotalTimeAttribute(): int
    {
        return $this->timeEntries()->sum('duration_seconds');
    }

    /**
     * Format total time as a human-readable string.
     */
    public function getFormattedTotalTimeAttribute(): string
    {
        $seconds = $this->total_time;
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Get priority as string.
     */
    public function getPriorityTextAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'low',
            self::PRIORITY_MEDIUM => 'medium',
            self::PRIORITY_HIGH => 'high',
            default => 'medium',
        };
    }

    /**
     * Set priority from string.
     */
    public function setPriorityAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['priority'] = match ($value) {
                'low' => self::PRIORITY_LOW,
                'medium' => self::PRIORITY_MEDIUM,
                'high' => self::PRIORITY_HIGH,
                default => self::PRIORITY_MEDIUM,
            };
        } else {
            $this->attributes['priority'] = $value;
        }
    }
}
