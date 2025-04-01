<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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
     * Get completed tasks for the user.
     */
    public function completedTasks()
    {
        return $this->tasks()->completed();
    }
    
    /**
     * Get incomplete tasks for the user.
     */
    public function incompleteTasks()
    {
        return $this->tasks()->incomplete();
    }
    
    /**
     * Get overdue tasks for the user.
     */
    public function overdueTasks()
    {
        return $this->tasks()->overdue();
    }
    
    /**
     * Get tasks due today for the user.
     */
    public function todayTasks()
    {
        return $this->tasks()->dueToday();
    }
    
    /**
     * Get tasks due this week for the user.
     */
    public function thisWeekTasks()
    {
        return $this->tasks()->dueThisWeek();
    }
    
    /**
     * Get tasks with no due date.
     */
    public function unscheduledTasks()
    {
        return $this->tasks()->whereNull('due_date');
    }
    
    /**
     * Get total time entries duration.
     */
    public function getTotalTrackedTimeAttribute(): int
    {
        return $this->timeEntries()->sum('duration_seconds');
    }
    
    /**
     * Format total tracked time as human-readable.
     */
    public function getFormattedTotalTrackedTimeAttribute(): string
    {
        $seconds = $this->total_tracked_time;
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
