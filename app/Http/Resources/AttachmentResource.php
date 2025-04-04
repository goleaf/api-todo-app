<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'filename' => $this->filename,
            'original_filename' => $this->original_filename,
            'file_size' => $this->file_size,
            'file_type' => $this->file_type,
            'path' => $this->path,
            'url' => $this->url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 