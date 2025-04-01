<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimeEntry extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_id',
        'user_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'description',
        'is_billable',
        'hourly_rate',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_billable' => 'boolean',
        'hourly_rate' => 'decimal:2',
    ];
    
    /**
     * Get the task that the time entry belongs to.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
    
    /**
     * Get the user that owns the time entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Start a new time entry.
     */
    public static function start(Task $task, ?string $description = null): self
    {
        return self::create([
            'task_id' => $task->id,
            'user_id' => auth()->id(),
            'started_at' => Carbon::now(),
            'description' => $description,
        ]);
    }
    
    /**
     * Stop the time entry.
     */
    public function stop(): self
    {
        $now = Carbon::now();
        $this->ended_at = $now;
        
        // Calculate duration
        $startedAt = Carbon::parse($this->started_at);
        $this->duration_seconds = $now->diffInSeconds($startedAt);
        
        $this->save();
        
        return $this;
    }
    
    /**
     * Check if this time entry is currently running.
     */
    public function isRunning(): bool
    {
        return is_null($this->ended_at);
    }
    
    /**
     * Format duration as a human-readable string.
     */
    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->duration_seconds;
        
        if ($this->isRunning()) {
            $seconds = Carbon::now()->diffInSeconds($this->started_at);
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }
    
    /**
     * Calculate cost based on billable status and hourly rate.
     */
    public function getCostAttribute(): ?float
    {
        if (!$this->is_billable || is_null($this->hourly_rate)) {
            return null;
        }
        
        $hours = $this->duration_seconds / 3600;
        return $hours * $this->hourly_rate;
    }
}
