<?php

namespace App\Services\Notifications;

use App\Enums\NotificationAudienceType;
use App\Enums\NotificationDeliveryStatus;
use App\Jobs\SendPushNotificationJob;
use App\Models\PushNotification;
use App\Models\PushNotificationRecipient;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function __construct(
        private readonly NotificationAudienceResolver $audienceResolver,
    ) {
    }

    public function queue(NotificationAudience $audience, ?User $initiator = null): PushNotification
    {
        return DB::transaction(function () use ($audience, $initiator): PushNotification {
            $users = $this->audienceResolver->resolveUsers($audience);

            $notification = PushNotification::create([
                'type' => $audience->type,
                'title' => $audience->title,
                'body' => $audience->body,
                'data' => $audience->data,
                'audience_type' => $audience->audienceType,
                'audience_value' => $this->audienceValue($audience),
                'initiator_id' => $initiator?->id,
                'queued_at' => now(),
            ]);

            $this->createRecipients($notification, $users, $audience);

            SendPushNotificationJob::dispatch($notification->id);

            return $notification->load('recipients.user');
        });
    }

    public function markAsRead(PushNotificationRecipient $recipient): PushNotificationRecipient
    {
        $recipient->forceFill([
            'status' => NotificationDeliveryStatus::Read,
            'read_at' => now(),
        ])->save();

        return $recipient;
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function createRecipients(PushNotification $notification, Collection $users, NotificationAudience $audience): void
    {
        $rows = $users
            ->unique('id')
            ->map(fn (User $user): array => [
                'push_notification_id' => $notification->id,
                'user_id' => $user->id,
                'topic' => $audience->audienceType === NotificationAudienceType::Topic ? $audience->topic?->value : null,
                'status' => NotificationDeliveryStatus::Pending->value,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        if ($rows !== []) {
            PushNotificationRecipient::query()->insert($rows);
        }
    }

    private function audienceValue(NotificationAudience $audience): ?string
    {
        return match ($audience->audienceType) {
            NotificationAudienceType::Users => implode(',', $audience->userIds),
            NotificationAudienceType::UserType => $audience->userType?->value,
            NotificationAudienceType::Topic => $audience->topic?->value,
        };
    }
}