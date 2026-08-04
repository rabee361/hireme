<?php

namespace App\Http\Resources;

use App\Models\ProjectApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ProjectApplication */
class ProjectApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_profile_id' => $this->student_profile_id,
            'project_id' => $this->project_id,
            'github_link' => $this->github_link,
            'expected_salary' => $this->expected_salary,
            'resume' => $this->resume,
            'status' => $this->status,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'student' => $this->whenLoaded('studentProfile', function () {
                $student = $this->studentProfile->user;

                return $student ? new StudentResource($student) : null;
            }),
            'project' => $this->whenLoaded('project', function () {
                return new ProjectResource($this->project);
            }),
        ];
    }
}
