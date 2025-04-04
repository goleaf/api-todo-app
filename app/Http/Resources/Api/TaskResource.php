<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date,
            'category_id' => $this->category_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'time_entries' => TimeEntryResource::collection($this->whenLoaded('timeEntries')),
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 