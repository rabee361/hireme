<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushNotificationRecipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = PushNotificationRecipient::query()
            ->with('notification')
            ->where('user_id', $request->user('api')->id)
            ->latest()
            ->get()
            ->map(fn (PushNotificationRecipient $recipient): array => [
                'id' => $recipient->id,
                'status' => $recipient->status?->value,
                'read_at' => $recipient->read_at?->toISOString(),
                'sent_at' => $recipient->sent_at?->toISOString(),
                'notification' => [
                    'id' => $recipient->notification?->id,
                    'type' => $recipient->notification?->type?->value,
                    'title' => $recipient->notification?->title,
                    'body' => $recipient->notification?->body,
                    'data' => $recipient->notification?->data ?? [],
                    'created_at' => $recipient->notification?->created_at?->toISOString(),
                ],
            ])
            ->all();

        return response()->json([
            'message' => 'Notifications retrieved successfully.',
            'data' => $notifications,
        ]);
    }

    public function markAsRead(Request $request, PushNotificationRecipient $pushNotificationRecipient): JsonResponse
    {
        abort_unless($pushNotificationRecipient->user_id === $request->user('api')->id, 403);

        $pushNotificationRecipient->forceFill([
            'status' => 'read',
            'read_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Notification marked as read.',
        ]);
    }
}