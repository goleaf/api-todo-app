<?php

namespace App\Models;

use App\Enums\CategoryType;
use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyDeep;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Tonysm\RichTextLaravel\Models\Traits\HasRichText;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    use HasRichText;
    use HasRelationships;
    use Sortable;

    /**
     * The columns that can be sorted.
     *
     * @var array<int, string>
     */
    public $sortable = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'user_id',
        'type'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'type',
        'user_id',
    ];

    /**
     * The rich text attributes for the model.
     *
     * @var array<int, string>
     */
    protected $richTextAttributes = [
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => CategoryType::class,
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'task_count',
        'completed_task_count',
    ];

    /**
     * Get the user that owns the category.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks for the category.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get all comments on tasks in this category.
     * 
     * This uses HasManyDeep to reach comments through tasks via the commentable relation.
     */
    public function taskComments(): HasManyDeep
    {
        return $this->hasManyDeep(
            Comment::class,
            [Task::class],
            [
                'category_id',   // Foreign key on the tasks table
                'commentable_id' // Foreign key on the comments table
            ],
            [
                'id', // Local key on the categories table
                'id'  // Local key on the tasks table
            ],
            [
                null,
                'commentable_type' => Task::class // Add where commentable_type is 'App\Models\Task'
            ]
        );
    }

    /**
     * Get all tags used in tasks for this category.
     * 
     * This uses HasManyDeep to reach tags through the task_tag pivot table.
     */
    public function taskTags(): HasManyDeep
    {
        return $this->hasManyDeep(
            Tag::class,
            [Task::class, 'task_tag'],
            [
                'category_id', // Foreign key on the tasks table
                'task_id',     // Foreign key on the task_tag table
                'id'          // Foreign key on the tags table
            ],
            [
                'id',     // Local key on the categories table
                'id',     // Local key on the tasks table
                'tag_id'  // Local key on the pivot table
            ]
        );
    }

    /**
     * Get all task attachments in this category.
     * 
     * This allows searching for files across all tasks in a category.
     */
    public function taskAttachments(): HasManyDeep
    {
        return $this->hasManyDeep(
            'task_attachments',
            [Task::class],
            [
                'category_id', // Foreign key on the tasks table
                'task_id'      // Foreign key on the attachments table
            ],
            [
                'id', // Local key on the categories table
                'id'  // Local key on the tasks table
            ]
        );
    }

    /**
     * Get the completed tasks for the category.
     */
    public function completedTasks(): HasMany
    {
        return $this->tasks()->where('completed', true);
    }

    /**
     * Get the incomplete tasks for the category.
     */
    public function incompleteTasks(): HasMany
    {
        return $this->tasks()->where('completed', false);
    }

    /**
     * Get the task count for the category.
     */
    public function getTaskCountAttribute(): int
    {
        return $this->tasks_count ?? $this->tasks()->count();
    }

    /**
     * Get the completed task count for the category.
     */
    public function getCompletedTaskCountAttribute(): int
    {
        return $this->completed_tasks_count ?? $this->completedTasks()->count();
    }

    /**
     * Get the completion percentage for the category.
     */
    public function completionPercentage(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $total = $this->task_count;
                if ($total === 0) {
                    return 0;
                }

                return ($this->completed_task_count / $total) * 100;
            }
        );
    }

    /**
     * Scope a query to only include categories for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to order categories by name.
     */
    public function scopeOrderByName(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    /**
     * Scope a query to only include categories with tasks.
     */
    public function scopeWithTasks(Builder $query): Builder
    {
        return $query->whereHas('tasks');
    }

    /**
     * Scope a query to only include categories with incomplete tasks.
     */
    public function scopeWithIncompleteTasks(Builder $query): Builder
    {
        return $query->whereHas('tasks', function ($query) {
            $query->where('completed', false);
        });
    }

    /**
     * Filter categories by those that have tasks with the given tag.
     */
    public function scopeWithTag(Builder $query, string $tag): Builder
    {
        return $query->whereHas('tasks', function ($query) use ($tag) {
            $query->whereJsonContains('tags', $tag);
        });
    }

    /**
     * Scope a query to search categories by name.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Load task counts efficiently.
     */
    public function scopeWithTaskCounts(Builder $query): Builder
    {
        return $query->withCount([
            'tasks as tasks_count',
            'tasks as completed_tasks_count' => function (Builder $query) {
                $query->where('completed', true);
            }
        ]);
    }
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($category) {
            event(new CategoryCreated($category));
        });

        static::updated(function ($category) {
            if ($category->wasChanged()) {
                event(new CategoryUpdated($category));
            }
        });

        static::deleted(function ($category) {
            event(new CategoryDeleted($category));
        });
    }
}
