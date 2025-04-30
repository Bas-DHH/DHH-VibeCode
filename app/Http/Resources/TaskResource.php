<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'name' => [
                'nl' => $this->name_nl,
                'en' => $this->name_en,
            ],
            'description' => $this->description,
            'instructions' => [
                'nl' => $this->instructions_nl,
                'en' => $this->instructions_en,
            ],
            'frequency' => $this->frequency,
            'scheduled_time' => $this->scheduled_time,
            'day_of_week' => $this->when($this->frequency === 'weekly', $this->day_of_week),
            'day_of_month' => $this->when($this->frequency === 'monthly', $this->day_of_month),
            'is_active' => $this->is_active,
            'category' => new TaskCategoryResource($this->whenLoaded('category')),
            'assigned_user' => new UserResource($this->whenLoaded('assignedUser')),
            'instances' => TaskInstanceResource::collection($this->whenLoaded('instances')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 