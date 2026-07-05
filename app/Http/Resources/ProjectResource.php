<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/** @mixin Project */
class ProjectResource extends JsonResource
{
    private const PROJECT_PAYLOAD_FIELDS = [
        'id',
        'name',
        'details',
        'tool1',
        'tool2',
        'tool3',
        'tool4',
        'tool5',
        'cover_image',
        'customer_id',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customer = $this->customer;
        $payload = $this->only(self::PROJECT_PAYLOAD_FIELDS);

        $payload['applicants_count'] = (int) ($this->applications_count ?? 0);
        $payload['customer'] = [
            'id' => $customer?->id,
            'name' => $customer?->username,
            'description' => $customer?->description,
            'image' => $customer?->avatar ?: $customer?->cover_image,
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
