<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'date_format',
        'time_format',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the tasks for the user.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the categories for the user.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
    
    /**
     * Get the tags for the user.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
    
    /**
     * Get the smart tags for the user.
     */
    public function smartTags(): HasMany
    {
        return $this->hasMany(SmartTag::class);
    }
    
    /**
     * Get the time entries for the user.
     */
    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }
    
    /**
     * Get the attachments uploaded by the user.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
    
    /**
     * Get the user's pending tasks.
     */
    public function pendingTasks(): HasMany
    {
        return $this->tasks()->where('completed', false);
    }
    
    /**
     * Get the user's completed tasks.
     */
    public function completedTasks(): HasMany
    {
        return $this->tasks()->where('completed', true);
    }
    
    /**
     * Get the user's overdue tasks.
     */
    public function overdueTasks(): HasMany
    {
        return $this->tasks()
            ->where('completed', false)
            ->where('due_date', '<', now());
    }
    
    /**
     * Get the user's upcoming tasks.
     */
    public function upcomingTasks(): HasMany
    {
        return $this->tasks()
            ->where('completed', false)
            ->where('due_date', '>', now());
    }
    
    /**
     * Get the user's running time entries.
     */
    public function runningTimeEntries(): HasMany
    {
        return $this->timeEntries()->whereNull('ended_at');
    }
    
    /**
     * Get the user's completed time entries.
     */
    public function completedTimeEntries(): HasMany
    {
        return $this->timeEntries()->whereNotNull('ended_at');
    }
    
    /**
     * Get total time spent on all tasks.
     */
    public function getTotalTimeSpentAttribute(): int
    {
        return $this->timeEntries()->sum('duration_seconds');
    }
    
    /**
     * Get the total time spent as a human-readable string.
     */
    public function getTotalTimeSpentFormattedAttribute(): string
    {
        $seconds = $this->total_time_spent;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
