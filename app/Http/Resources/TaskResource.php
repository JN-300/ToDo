<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class TaskResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'deadline' => $this->deadline->toIso8601String(),

            'owner' => new UserResource($this->whenLoaded('owner')),
            'project_id' => $this->project_id,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at

        ];
    }
}
