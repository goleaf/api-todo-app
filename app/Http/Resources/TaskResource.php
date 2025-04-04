<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'completed' => (bool) $this->completed,
            'completed_at' => $this->completed_at,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'color' => $this->category->color,
                ];
            }),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'time_entries' => TimeEntryResource::collection($this->whenLoaded('timeEntries')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'total_time' => $this->whenLoaded('timeEntries', function () {
                return $this->timeEntries->sum('duration');
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 