<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class SmartTag extends Model
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
        'description',
        'user_id',
        'criteria',
        'filter_by_due_date',
        'due_date_operator',
        'due_date_values',
        'filter_by_priority',
        'priority_values',
        'filter_by_category',
        'category_ids',
        'filter_by_status',
        'status_completed',
        'status_pending',
        'status_in_progress',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'criteria' => 'json',
        'filter_by_due_date' => 'boolean',
        'due_date_values' => 'json',
        'filter_by_priority' => 'boolean',
        'priority_values' => 'json',
        'filter_by_category' => 'boolean',
        'category_ids' => 'json',
        'filter_by_status' => 'boolean',
        'status_completed' => 'boolean',
        'status_pending' => 'boolean',
        'status_in_progress' => 'boolean',
    ];
    
    /**
     * Get the user that owns the smart tag.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Apply this smart tag's criteria to the given task query
     */
    public function applyToQuery(Builder $query): Builder
    {
        $query = $query->where('user_id', $this->user_id);
        
        // Apply due date filters
        if ($this->filter_by_due_date) {
            switch ($this->due_date_operator) {
                case 'today':
                    $query->whereDate('due_date', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('due_date', [Carbon::today(), Carbon::today()->addDays(7)]);
                    break;
                case 'overdue':
                    $query->whereDate('due_date', '<', Carbon::today());
                    break;
                case 'custom':
                    if (!empty($this->due_date_values)) {
                        $dates = $this->due_date_values;
                        if (!empty($dates['start']) && !empty($dates['end'])) {
                            $query->whereBetween('due_date', [$dates['start'], $dates['end']]);
                        } elseif (!empty($dates['start'])) {
                            $query->whereDate('due_date', '>=', $dates['start']);
                        } elseif (!empty($dates['end'])) {
                            $query->whereDate('due_date', '<=', $dates['end']);
                        }
                    }
                    break;
            }
        }
        
        // Apply priority filters
        if ($this->filter_by_priority && !empty($this->priority_values)) {
            $query->whereIn('priority', $this->priority_values);
        }
        
        // Apply category filters
        if ($this->filter_by_category && !empty($this->category_ids)) {
            $query->whereIn('category_id', $this->category_ids);
        }
        
        // Apply status filters
        if ($this->filter_by_status) {
            $query->where(function($q) {
                if ($this->status_pending) {
                    $q->orWhere('status', 'pending');
                }
                if ($this->status_in_progress) {
                    $q->orWhere('status', 'in_progress');
                }
                if ($this->status_completed) {
                    $q->orWhere('status', 'completed');
                }
            });
        }
        
        // Apply any custom criteria
        if (!empty($this->criteria)) {
            // Custom complex criteria could be implemented here
            // This is where you'd handle advanced query conditions
            $criteria = $this->criteria;
            
            // Search in title or description
            if (isset($criteria['search']) && !empty($criteria['search'])) {
                $search = $criteria['search'];
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Filter by tags
            if (isset($criteria['tags']) && !empty($criteria['tags'])) {
                $tagIds = $criteria['tags'];
                $query->whereHas('tags', function($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            }
            
            // Filter by time tracking
            if (isset($criteria['has_time_entries'])) {
                if ($criteria['has_time_entries']) {
                    $query->whereHas('timeEntries');
                } else {
                    $query->whereDoesntHave('timeEntries');
                }
            }
            
            // Filter by attachments
            if (isset($criteria['has_attachments'])) {
                if ($criteria['has_attachments']) {
                    $query->whereHas('attachments');
                } else {
                    $query->whereDoesntHave('attachments');
                }
            }
            
            // Filter by creation date
            if (isset($criteria['created_date']) && !empty($criteria['created_date'])) {
                $createdDate = $criteria['created_date'];
                if (isset($createdDate['start']) && !empty($createdDate['start'])) {
                    $query->whereDate('created_at', '>=', $createdDate['start']);
                }
                if (isset($createdDate['end']) && !empty($createdDate['end'])) {
                    $query->whereDate('created_at', '<=', $createdDate['end']);
                }
            }
        }
        
        return $query;
    }
    
    /**
     * Get all tasks matching this smart tag's criteria
     */
    public function getMatchingTasks()
    {
        $query = Task::query();
        return $this->applyToQuery($query)->get();
    }
    
    /**
     * Get the count of tasks matching this smart tag's criteria
     */
    public function getTaskCountAttribute(): int
    {
        $query = Task::query();
        return $this->applyToQuery($query)->count();
    }
}
