<?php

namespace App\Services\Notifications;

use App\Enums\NotificationAudienceType;
use App\Enums\NotificationTopic;
use App\Enums\NotificationType;
use App\Enums\UserType;

class NotificationAudience
{
    /**
     * @param  array<int, int>  $userIds
     */
    public function __construct(
        public readonly NotificationType $type,
        public readonly string $title,
        public readonly string $body,
        public readonly NotificationAudienceType $audienceType,
        public readonly array $userIds = [],
        public readonly ?UserType $userType = null,
        public readonly ?NotificationTopic $topic = null,
        public readonly array $data = [],
    ) {
    }

    public static function forUsers(NotificationType $type, string $title, string $body, array $userIds, array $data = []): self
    {
        return new self(
            type: $type,
            title: $title,
            body: $body,
            audienceType: NotificationAudienceType::Users,
            userIds: $userIds,
            data: $data,
        );
    }

    public static function forUserType(NotificationType $type, string $title, string $body, UserType $userType, array $data = []): self
    {
        return new self(
            type: $type,
            title: $title,
            body: $body,
            audienceType: NotificationAudienceType::UserType,
            userType: $userType,
            data: $data,
        );
    }

    public static function forTopic(NotificationType $type, string $title, string $body, NotificationTopic $topic, array $data = []): self
    {
        return new self(
            type: $type,
            title: $title,
            body: $body,
            audienceType: NotificationAudienceType::Topic,
            topic: $topic,
            data: $data,
        );
    }
}