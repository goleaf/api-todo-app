<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
        'user_id',
        'usage_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'usage_count' => 'integer',
    ];

    /**
     * Get the user that owns the tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks that belong to the tag.
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_tag');
    }

    /**
     * Scope a query to only include tags for a specific user.
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to order tags by usage count.
     */
    public function scopeOrderByUsage(Builder $query): Builder
    {
        return $query->orderBy('usage_count', 'desc');
    }

    /**
     * Scope a query to order tags by name.
     */
    public function scopeOrderByName(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    /**
     * Scope a query to search tags by name.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'like', "%{$search}%");
    }

    /**
     * Scope a query to find tags by partial name match.
     */
    public function scopeNameLike(Builder $query, string $partialName): Builder
    {
        return $query->where('name', 'like', "%{$partialName}%");
    }

    /**
     * Increment the usage count for the tag.
     */
    public function incrementUsage(): bool
    {
        $this->usage_count++;
        return $this->save();
    }

    /**
     * Decrement the usage count for the tag.
     */
    public function decrementUsage(): bool
    {
        if ($this->usage_count > 0) {
            $this->usage_count--;
            return $this->save();
        }
        
        return false;
    }

    /**
     * Check if a tag with the given name exists for the user.
     */
    public static function existsForUser(string $name, int $userId): bool
    {
        return static::where('name', $name)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Find or create a tag with the given name for the user.
     */
    public static function findOrCreateForUser(string $name, int $userId, ?string $color = null): self
    {
        $tag = static::where('name', $name)
            ->where('user_id', $userId)
            ->first();
            
        if (!$tag) {
            $tag = static::create([
                'name' => $name,
                'user_id' => $userId,
                'color' => $color ?? self::generateDefaultColor($name),
                'usage_count' => 0,
            ]);
        }
        
        return $tag;
    }

    /**
     * Get tags matching a partial name for a user.
     */
    public static function getMatchingForUser(string $partialName, int $userId, int $limit = 10): Builder
    {
        return static::forUser($userId)
            ->namelike($partialName)
            ->orderByUsage()
            ->limit($limit);
    }

    /**
     * Generate a default color based on the tag name.
     */
    public static function generateDefaultColor(string $name): string
    {
        return '#' . substr(md5($name), 0, 6);
    }
}
