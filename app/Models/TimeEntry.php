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
        'user_id',
        'task_id',
        'started_at',
        'ended_at',
        'description',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
    
    /**
     * Get the user that owns the time entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the task that owns the time entry.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
    
    /**
     * Scope a query to only include time entries for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Scope a query to only include running time entries.
     */
    public function scopeRunning($query)
    {
        return $query->whereNull('ended_at');
    }
    
    /**
     * Scope a query to only include completed time entries.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }
    
    /**
     * Get the duration of the time entry in seconds.
     */
    public function getDurationSecondsAttribute(): int
    {
        if (!$this->ended_at) {
            return Carbon::now()->diffInSeconds($this->started_at);
        }
        
        return $this->ended_at->diffInSeconds($this->started_at);
    }
    
    /**
     * Get the duration of the time entry as a human-readable string.
     */
    public function getDurationAttribute(): string
    {
        $seconds = $this->duration_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
    
    /**
     * Stop the time entry.
     */
    public function stop(): bool
    {
        $this->ended_at = now();
        return $this->save();
    }
    
    /**
     * Check if the time entry is currently running.
     */
    public function isRunning(): bool
    {
        return is_null($this->ended_at);
    }
}
