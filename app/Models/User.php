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
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'active',
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
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo_path ? asset('storage/'.$this->photo_path) : null,
        );
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
        $search = '%' . $search . '%';
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'like', $search)
                ->orWhere('email', 'like', $search);
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
        // Use a single query to get task counts by completion status
        $taskCounts = $this->tasks()
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed'),
                DB::raw('SUM(CASE WHEN completed = 0 THEN 1 ELSE 0 END) as incomplete')
            )
            ->first();
            
        // Get counts for specific task types
        $todayCount = $this->tasksDueToday()->count();
        $overdueCount = $this->overdueTasks()->count();
        $upcomingCount = $this->upcomingTasks()->count();
        
        // Get priority counts in a single query
        $priorityCounts = $this->tasks()
            ->select('priority', DB::raw('COUNT(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();
            
        $total = $taskCounts->total ?? 0;
        $completed = $taskCounts->completed ?? 0;
        
        return [
            'total' => $total,
            'completed' => $completed,
            'incomplete' => $taskCounts->incomplete ?? 0,
            'today' => $todayCount,
            'overdue' => $overdueCount,
            'upcoming' => $upcomingCount,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
            'by_priority' => [
                '1' => $priorityCounts[1] ?? 0,
                '2' => $priorityCounts[2] ?? 0,
                '3' => $priorityCounts[3] ?? 0,
                '4' => $priorityCounts[4] ?? 0,
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
        
        // Use a join to get category counts in a single query
        $categoryCounts = Category::select('categories.id', 'categories.name', DB::raw('COUNT(tasks.id) as count'))
            ->leftJoin('tasks', 'categories.id', '=', 'tasks.category_id')
            ->where('categories.user_id', $this->id)
            ->groupBy('categories.id', 'categories.name')
            ->get();
            
        foreach ($categoryCounts as $category) {
            $result[$category->id] = [
                'name' => $category->name,
                'count' => $category->count,
            ];
        }
        
        return $result;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        if ($this->role instanceof UserRole) {
            return $this->role === UserRole::ADMIN;
        }
        
        return $this->role === UserRole::ADMIN->value || $this->role === 'admin';
    }
}