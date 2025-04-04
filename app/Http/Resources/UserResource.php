<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => (bool) $this->is_admin,
            'is_active' => (bool) $this->is_active,
            'avatar' => $this->avatar,
            'language' => $this->language,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Only include these for admin requests or the user themselves
            'email_verified_at' => $this->when(
                $request->user()?->is_admin || $request->user()?->id === $this->id,
                $this->email_verified_at
            ),
            'task_count' => $this->whenCounted('tasks'),
            'time_entries_count' => $this->whenCounted('timeEntries'),
            'categories_count' => $this->whenCounted('categories'),
            'tags_count' => $this->whenCounted('tags'),
        ];
    }
} 