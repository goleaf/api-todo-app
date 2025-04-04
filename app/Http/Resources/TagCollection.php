<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TagCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->resource->total(),
                'count' => $this->resource->count(),
                'per_page' => $this->resource->perPage(),
                'current_page' => $this->resource->currentPage(),
                'last_page' => $this->resource->lastPage(),
                'from' => $this->resource->firstItem(),
                'to' => $this->resource->lastItem(),
            ],
        ];
    }
} 