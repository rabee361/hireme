<?php

namespace App\Services\Auth;

use App\Models\DeviceToken;
use App\Models\User;

class DeviceTokenService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function storeFromPayload(User $user, array $payload): ?DeviceToken
    {
        if (! $this->hasCompleteDevicePayload($payload)) {
            return null;
        }

        return $user->deviceTokens()->updateOrCreate(
            ['device_id' => (string) $payload['device_id']],
            [
                'fcm_token' => (string) $payload['fcm_token'],
                'platform' => (string) $payload['platform'],
                'app_version' => isset($payload['app_version']) ? (string) $payload['app_version'] : null,
                'is_active' => true,
                'last_seen_at' => now(),
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function hasCompleteDevicePayload(array $payload): bool
    {
        return filled($payload['device_id'] ?? null)
            && filled($payload['fcm_token'] ?? null)
            && filled($payload['platform'] ?? null);
    }
}