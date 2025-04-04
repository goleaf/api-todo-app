<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeEntryResource extends JsonResource
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
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
            'description' => $this->description,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration' => $this->duration,
            'is_running' => $this->is_running,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 