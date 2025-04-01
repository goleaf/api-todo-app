<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Oddvalue\LaravelDrafts\Concerns\HasDrafts;

class Post extends Model
{
    use HasFactory;
    use HasDrafts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'slug',
        'user_id',
    ];

    /**
     * The relations that should be included when publishing a draft.
     *
     * @var array
     */
    protected array $draftableRelations = [];

    /**
     * Get the user that owns the post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
