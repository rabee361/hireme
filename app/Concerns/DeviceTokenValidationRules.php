<?php

namespace App\Concerns;

trait DeviceTokenValidationRules
{
    /**
     * @return array<string, array<int, mixed>>
     */
    protected function optionalDeviceTokenRules(): array
    {
        return [
            'device_id' => ['nullable', 'string', 'max:255', 'required_with:fcm_token,platform,app_version'],
            'fcm_token' => ['nullable', 'string', 'max:2048', 'required_with:device_id,platform,app_version'],
            'platform' => ['nullable', 'string', 'in:android,ios,web', 'required_with:device_id,fcm_token,app_version'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function requiredDeviceTokenRules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:255'],
            'fcm_token' => ['required', 'string', 'max:2048'],
            'platform' => ['required', 'string', 'in:android,ios,web'],
            'app_version' => ['nullable', 'string', 'max:50'],
        ];
    }
}