<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

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
        'priority',
        'completed',
        'category_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Priority levels.
     */
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

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
     * Get the tags associated with the task.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
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
}
