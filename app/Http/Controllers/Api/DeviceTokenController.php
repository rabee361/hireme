<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\StoreDeviceTokenRequest;
use App\Models\DeviceToken;
use App\Services\Auth\DeviceTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tokens = $request->user('api')
            ->deviceTokens()
            ->latest()
            ->get()
            ->map(fn (DeviceToken $token): array => [
                'id' => $token->id,
                'device_id' => $token->device_id,
                'platform' => $token->platform?->value,
                'app_version' => $token->app_version,
                'is_active' => $token->is_active,
                'last_seen_at' => $token->last_seen_at?->toISOString(),
            ])
            ->all();

        return response()->json([
            'message' => 'Device tokens retrieved successfully.',
            'data' => $tokens,
        ]);
    }

    public function store(StoreDeviceTokenRequest $request, DeviceTokenService $deviceTokenService): JsonResponse
    {
        $token = $deviceTokenService->storeFromPayload($request->user('api'), $request->validated());

        return response()->json([
            'message' => 'Device token saved successfully.',
            'data' => [
                'id' => $token->id,
                'device_id' => $token->device_id,
                'platform' => $token->platform?->value,
                'is_active' => $token->is_active,
            ],
        ], 201);
    }

    public function destroy(Request $request, DeviceToken $deviceToken): JsonResponse
    {
        abort_unless($deviceToken->user_id === $request->user('api')->id, 403);

        $deviceToken->delete();

        return response()->json([
            'message' => 'Device token removed successfully.',
        ]);
    }
}