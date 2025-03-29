<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'due_date',
        'reminder_at',
        'priority',
        'progress',
        'category_id',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'datetime',
        'reminder_at' => 'datetime',
        'priority' => 'integer',
        'progress' => 'integer',
    ];

    protected $with = ['category'];

    /**
     * Get the user that owns the todo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category that the todo belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
