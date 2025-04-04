<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmartTagResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'color' => $this->color,
            'criteria' => $this->criteria,
            'tasks_count' => $this->whenCounted('tasks'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 