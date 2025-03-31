<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
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
        'profile_photo_path',
        'photo_path',
        'role',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'photo_url',
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
     * Get the device tokens for the user.
     */
    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    /**
     * Get the tags for the user.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    /**
     * Get the user's photo URL.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/'.$this->photo_path);
        }

        return null;
    }

    /**
     * Determine if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Get the completed tasks for the user.
     */
    public function completedTasks(): HasMany
    {
        return $this->tasks()->where('completed', true);
    }

    /**
     * Get the incomplete tasks for the user.
     */
    public function incompleteTasks(): HasMany
    {
        return $this->tasks()->where('completed', false);
    }

    /**
     * Get tasks due today.
     */
    public function tasksDueToday(): HasMany
    {
        return $this->tasks()->dueToday();
    }

    /**
     * Get overdue tasks.
     */
    public function overdueTasks(): HasMany
    {
        return $this->tasks()->overdue();
    }

    /**
     * Get upcoming tasks.
     */
    public function upcomingTasks(): HasMany
    {
        return $this->tasks()->upcoming();
    }

    /**
     * Scope a query to search users by name or email.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeWithRole(Builder $query, UserRole $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * Get task statistics for the user.
     */
    public function getTaskStatistics(): array
    {
        $total = $this->tasks()->count();
        $completed = $this->completedTasks()->count();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'incomplete' => $total - $completed,
            'today' => $this->tasksDueToday()->count(),
            'overdue' => $this->overdueTasks()->count(),
            'upcoming' => $this->upcomingTasks()->count(),
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            'by_priority' => [
                '1' => $this->tasks()->withPriority(1)->count(),
                '2' => $this->tasks()->withPriority(2)->count(),
                '3' => $this->tasks()->withPriority(3)->count(),
                '4' => $this->tasks()->withPriority(4)->count(),
            ],
            'by_category' => $this->getCategoryStatistics(),
        ];
    }
    
    /**
     * Get task statistics by category.
     */
    private function getCategoryStatistics(): array
    {
        $result = [];
        $categories = $this->categories()->get();
        
        foreach ($categories as $category) {
            $result[$category->id] = [
                'name' => $category->name,
                'count' => $category->tasks()->count(),
            ];
        }
        
        return $result;
    }
}

