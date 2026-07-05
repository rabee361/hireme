<?php

namespace App\Http\Resources;

use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Ad */
class AdResource extends JsonResource
{
    private const AD_PAYLOAD_FIELDS = [
        'id',
        'job_name',
        'req1',
        'req2',
        'req3',
        'req4',
        'req5',
        'task1',
        'task2',
        'task3',
        'task4',
        'task5',
        'feature1',
        'feature2',
        'feature3',
        'feature4',
        'feature5',
        'additional_details',
        'github_required',
        'resume_required',
        'prev_work_required',
        'expected_salary_required',
        'company_id',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $company = $this->company;
        $payload = $this->only(self::AD_PAYLOAD_FIELDS);

        $payload['applicants_count'] = (int) ($this->applications_count ?? 0);
        $payload['company'] = [
            'id' => $company?->id,
            'name' => $company?->username,
            'description' => $company?->description,
            'image' => $company?->avatar ?: $company?->cover_image,
        ];
        $payload['since'] = $this->since();
        $payload['created_at'] = $this->created_at?->toISOString();
        $payload['updated_at'] = $this->updated_at?->toISOString();

        return $payload;
    }

    private function since(): ?string
    {
        if (! $this->created_at) {
            return null;
        }

        $days = $this->created_at->diffInDays(now());

        if ($days >= 1) {
            return $days.' '.Str::plural('day', $days).' ago';
        }

        $hours = $this->created_at->diffInHours(now());

        if ($hours >= 1) {
            return $hours.' '.Str::plural('hour', $hours).' ago';
        }

        return 'less than an hour ago';
    }
}
