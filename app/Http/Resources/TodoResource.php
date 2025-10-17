<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    { 
        return [
            'id' => $this->id,
            'title' => $this->title,
            'assignee' => $this->assignee,
            'due_date' => $this->due_date,
            'time_tracked' => (float) $this->time_tracked,
            'status' => $this->status,
            'priority' => $this->priority,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
