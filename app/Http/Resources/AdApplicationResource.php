<?php

namespace App\Http\Resources;

use App\Models\AdApplication;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AdApplication */
class AdApplicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_profile_id' => $this->student_profile_id,
            'ad_id' => $this->ad_id,
            'github_link' => $this->github_link,
            'expected_salary' => $this->expected_salary,
            'resume' => $this->resume,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'student' => $this->whenLoaded('studentProfile', function () {
                $student = $this->studentProfile->user;

                return $student ? new StudentResource($student) : null;
            }),
            'ad' => $this->whenLoaded('ad', function () {
                return new AdResource($this->ad);
            }),
        ];
    }
}
