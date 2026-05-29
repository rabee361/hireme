<?php

namespace App\Jobs;

use App\Enums\NotificationAudienceType;
use App\Enums\NotificationDeliveryStatus;
use App\Models\DeviceToken;
use App\Models\PushNotification;
use App\Services\Notifications\FirebaseMessagingClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPushNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $notificationId,
    ) {
    }

    public function handle(FirebaseMessagingClient $messagingClient): void
    {
        $notification = PushNotification::query()
            ->with(['recipients.user.deviceTokens'])
            ->find($this->notificationId);

        if (! $notification) {
            return;
        }

        if ($notification->audience_type === NotificationAudienceType::Topic && $notification->audience_value) {
            $messagingClient->sendToTopic(
                $notification->audience_value,
                $notification->title,
                $notification->body,
                $notification->data ?? [],
            );

            $notification->recipients->each(function ($recipient): void {
                $recipient->forceFill([
                    'status' => NotificationDeliveryStatus::Sent,
                    'sent_at' => now(),
                ])->save();
            });

            $notification->forceFill([
                'sent_at' => now(),
            ])->save();

            return;
        }

        foreach ($notification->recipients as $recipient) {
            $tokens = $recipient->user?->deviceTokens()
                ->where('is_active', true)
                ->pluck('fcm_token')
                ->all() ?? [];

            if ($tokens === []) {
                $recipient->forceFill([
                    'status' => NotificationDeliveryStatus::Failed,
                    'failed_at' => now(),
                    'failure_reason' => 'No active device token.',
                ])->save();

                continue;
            }

            $report = $messagingClient->sendToTokens(
                $tokens,
                $notification->title,
                $notification->body,
                $notification->data ?? [],
            );

            $recipient->forceFill([
                'status' => ($report['failures'] ?? 0) > 0 ? NotificationDeliveryStatus::Failed : NotificationDeliveryStatus::Sent,
                'sent_at' => now(),
                'failed_at' => ($report['failures'] ?? 0) > 0 ? now() : null,
                'failure_reason' => ($report['failures'] ?? 0) > 0 ? 'One or more tokens failed.' : null,
            ])->save();

            if (($report['invalid_tokens'] ?? []) !== []) {
                DeviceToken::query()
                    ->whereIn('fcm_token', $report['invalid_tokens'])
                    ->update([
                        'is_active' => false,
                        'updated_at' => now(),
                    ]);
            }
        }

        $notification->forceFill([
            'sent_at' => now(),
        ])->save();
    }
}