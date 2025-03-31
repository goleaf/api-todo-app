<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskProgressStatus;
use App\Enums\TaskStatus;
use App\Events\TaskCompleted;
use App\Events\TaskCreated;
use App\Events\TaskDeleted;
use App\Events\TaskUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;
use Tonysm\RichTextLaravel\Models\Traits\HasRichText;
use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Relations\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;
    use HasRichText;
    use HasComments;
    use HasRelationships;
    use Sortable;

    /**
     * The columns that can be sorted.
     *
     * @var array<int, string>
     */
    public $sortable = [
        'id',
        'title',
        'due_date',
        'priority',
        'completed',
        'created_at',
        'updated_at',
        'category_id',
        'user_id'
    ];

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
        'progress',
        'completed_at',
    ];

    /**
     * The rich text attributes for the model.
     *
     * @var array<int, string>
     */
    protected $richTextAttributes = [
        'description',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'date',
        'priority' => TaskPriority::class,
        'tags' => 'array',
        'attachments' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * The relationships that should be eager loaded.
     *
     * @var array<int, string>
     */
    protected $with = ['category'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_due_date',
        'priority_label',
        'priority_color',
        'status',
        'progress_status',
    ];

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
        return $this->belongsToMany(Tag::class, 'task_tag');
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
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::today());
    }

    /**
     * Scope a query to only include upcoming tasks.
     */
    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->where('completed', false)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [
                Carbon::today(),
                Carbon::today()->addDays($days),
            ]);
    }

    /**
     * Scope a query to only include tasks with a specific priority.
     */
    public function scopeWithPriority(Builder $query, int|TaskPriority $priority): Builder
    {
        $priorityValue = $priority instanceof TaskPriority ? $priority->value : $priority;
        return $query->where('priority', $priorityValue);
    }

    /**
     * Scope a query to only include tasks with specific tags.
     */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->whereHas('tags', function ($query) use ($tag) {
            $query->where('name', $tag);
        });
    }

    /**
     * Scope a query to only include tasks with any of the specified tag IDs.
     */
    public function scopeWithAnyTag(Builder $query, array $tagIds): Builder
    {
        return $query->whereHas('tags', function ($query) use ($tagIds) {
            $query->whereIn('tags.id', $tagIds);
        });
    }

    /**
     * Scope a query to only include tasks with all of the specified tag IDs.
     */
    public function scopeWithAllTags(Builder $query, array $tagIds): Builder
    {
        foreach ($tagIds as $tagId) {
            $query->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }
        
        return $query;
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
        $searchTerm = '%' . $search . '%';
        return $query->where(function ($query) use ($searchTerm) {
            $query->where('title', 'like', $searchTerm)
                ->orWhere('description', 'like', $searchTerm)
                ->orWhere('notes', 'like', $searchTerm);
        });
    }

    /**
     * Toggle the completion status of a task.
     */
    public function toggleCompletion(): bool
    {
        $wasCompleted = $this->completed;
        $this->completed = !$this->completed;
        $this->completed_at = $this->completed ? now() : null;
        $this->progress = $this->completed ? 100 : ($this->progress ?? 0);
        
        $saved = $this->save();
        
        if ($saved && $this->completed && !$wasCompleted) {
            event(new TaskCompleted($this));
        }
        
        return $saved;
    }

    /**
     * Determine if the task is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->completed || !$this->due_date) {
            return false;
        }

        return $this->due_date->isPast();
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
        if (!$this->due_date || $this->completed) {
            return false;
        }

        return $this->due_date->isFuture() &&
               $this->due_date->lte(Carbon::today()->addDays($days));
    }

    /**
     * Get the formatted due date.
     */
    protected function formattedDueDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->due_date ? $this->due_date->format('Y-m-d') : null,
        );
    }

    /**
     * Get the priority label.
     */
    protected function priorityLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->priority ? $this->priority->label() : 'None',
        );
    }

    /**
     * Get the priority color.
     */
    protected function priorityColor(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->priority ? $this->priority->color() : 'secondary',
        );
    }

    /**
     * Get the status enum for this task.
     */
    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->completed ? TaskStatus::COMPLETE : TaskStatus::INCOMPLETE,
        );
    }

    /**
     * Get the progress status enum for this task.
     */
    protected function progressStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => TaskProgressStatus::fromPercentage($this->progress ?? 0),
        );
    }

    /**
     * Mark the task as complete.
     */
    public function markAsComplete(): bool
    {
        $this->completed = true;
        $this->completed_at = now();
        $this->progress = 100;
        
        $saved = $this->save();
        
        if ($saved) {
            event(new TaskCompleted($this));
        }
        
        return $saved;
    }

    /**
     * Mark the task as incomplete.
     */
    public function markAsIncomplete(): bool
    {
        $this->completed = false;
        $this->completed_at = null;
        
        return $this->save();
    }

    /**
     * Add multiple tags to the task.
     */
    public function addTags(array $tagIds): bool
    {
        $this->tags()->syncWithoutDetaching($tagIds);
        return true;
    }

    /**
     * Remove multiple tags from the task.
     */
    public function removeTags(array $tagIds): bool
    {
        $this->tags()->detach($tagIds);
        return true;
    }

    /**
     * Check if the task has a specific tag.
     */
    public function hasTag(int $tagId): bool
    {
        return $this->tags()->where('tags.id', $tagId)->exists();
    }

    /**
     * Get comment replies for this task.
     * 
     * This retrieves all reply comments (comments that have a parent comment)
     * that are associated with this task through the commentable relation.
     */
    public function commentReplies(): HasManyDeep
    {
        return $this->hasManyDeep(
            Comment::class,
            [Comment::class],
            [
                'commentable_id', // Foreign key on parent comments table
                'parent_id'       // Foreign key on replies (child comments) table
            ],
            [
                'id', // Local key on tasks table
                'id'  // Local key on comments table
            ],
            [
                'commentable_type' => self::class, // Ensure this is for Task model
                null
            ]
        );
    }

    /**
     * Get the users who have commented on this task.
     * 
     * This allows retrieving all users who have participated in discussions on this task.
     */
    public function commenters(): HasManyDeep
    {
        return $this->hasManyDeep(
            User::class,
            [Comment::class],
            [
                'commentable_id', // Foreign key on comments table
                'id'             // Foreign key on users table
            ],
            [
                'id',        // Local key on tasks table
                'user_id'    // Local key on comments table
            ],
            [
                'commentable_type' => self::class, // Ensure this is for Task model
                null
            ]
        )->distinct(); // Ensure each user is only returned once
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($task) {
            event(new TaskCreated($task));
        });

        static::updated(function ($task) {
            if ($task->wasChanged() && !$task->wasChanged('completed')) {
                event(new TaskUpdated($task));
            }
        });

        static::deleted(function ($task) {
            event(new TaskDeleted($task));
        });

        static::saved(function ($task) {
            // Clear any cached data related to this task
            $cacheKeys = [
                "user.{$task->user_id}.tasks",
                "category.{$task->category_id}.tasks",
            ];
            
            foreach ($cacheKeys as $key) {
                Cache::forget($key);
            }
        });
    }
}
