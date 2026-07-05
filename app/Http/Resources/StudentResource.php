<?php

namespace App\Http\Resources;

use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Student */
class StudentResource extends JsonResource
{
    private const USER_FIELDS = [
        'id',
        'username',
        'email',
        'phone_number',
        'description',
        'cover_image',
        'avatar',
        'is_verified',
    ];

    private const PROFILE_FIELDS = [
        'id',
        'user_id',
        'address',
        'hour_cost',
        'experience_years',
        'tech1',
        'tech2',
        'tech3',
        'college',
        'title',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $payload = $this->only(self::USER_FIELDS);
        $payload['type'] = $this->type?->value;
        $payload['created_at'] = $this->created_at?->toISOString();
        $payload['updated_at'] = $this->updated_at?->toISOString();
        $payload['profile'] = $this->profilePayload($this->profile);

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function profilePayload(?StudentProfile $profile): ?array
    {
        if (! $profile) {
            return null;
        }

        $payload = $profile->only(self::PROFILE_FIELDS);
        $payload['created_at'] = $profile->created_at?->toISOString();
        $payload['updated_at'] = $profile->updated_at?->toISOString();

        return $payload;
    }
}
