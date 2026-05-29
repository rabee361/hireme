<?php

use App\Enums\NotificationAudienceType;
use App\Enums\NotificationDeliveryStatus;
use App\Enums\NotificationType;
use App\Jobs\SendPushNotificationJob;
use App\Models\DeviceToken;
use App\Models\PushNotification;
use App\Models\PushNotificationRecipient;
use App\Models\User;
use App\Services\Notifications\FirebaseMessagingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sends user notifications to all active device tokens', function (): void {
    $user = User::factory()->create();

    DeviceToken::query()->create([
        'user_id' => $user->id,
        'device_id' => 'android-1',
        'fcm_token' => 'token-1',
        'platform' => 'android',
        'is_active' => true,
    ]);

    DeviceToken::query()->create([
        'user_id' => $user->id,
        'device_id' => 'ios-1',
        'fcm_token' => 'token-2',
        'platform' => 'ios',
        'is_active' => true,
    ]);

    $notification = PushNotification::query()->create([
        'type' => NotificationType::AdminAnnouncement,
        'title' => 'Title',
        'body' => 'Body',
        'audience_type' => NotificationAudienceType::Users,
        'audience_value' => (string) $user->id,
        'queued_at' => now(),
    ]);

    $recipient = PushNotificationRecipient::query()->create([
        'push_notification_id' => $notification->id,
        'user_id' => $user->id,
        'status' => NotificationDeliveryStatus::Pending,
    ]);

    $fakeClient = new class implements FirebaseMessagingClient
    {
        /**
         * @var array<int, string>
         */
        public array $tokens = [];

        public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
        {
            $this->tokens = $tokens;

            return [
                'successes' => count($tokens),
                'failures' => 0,
                'invalid_tokens' => [],
            ];
        }

        public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
        {
            return [
                'successes' => 1,
                'failures' => 0,
                'invalid_tokens' => [],
            ];
        }
    };

    app()->instance(FirebaseMessagingClient::class, $fakeClient);

    app(SendPushNotificationJob::class, ['notificationId' => $notification->id])->handle($fakeClient);

    expect($fakeClient->tokens)->toBe(['token-1', 'token-2']);

    $recipient->refresh();
    $notification->refresh();

    expect($recipient->status)->toBe(NotificationDeliveryStatus::Sent);
    expect($notification->sent_at)->not->toBeNull();
});

it('deactivates invalid device tokens reported by firebase', function (): void {
    $user = User::factory()->create();

    DeviceToken::query()->create([
        'user_id' => $user->id,
        'device_id' => 'android-1',
        'fcm_token' => 'invalid-token',
        'platform' => 'android',
        'is_active' => true,
    ]);

    $notification = PushNotification::query()->create([
        'type' => NotificationType::AdminAnnouncement,
        'title' => 'Title',
        'body' => 'Body',
        'audience_type' => NotificationAudienceType::Users,
        'audience_value' => (string) $user->id,
        'queued_at' => now(),
    ]);

    $recipient = PushNotificationRecipient::query()->create([
        'push_notification_id' => $notification->id,
        'user_id' => $user->id,
        'status' => NotificationDeliveryStatus::Pending,
    ]);

    $fakeClient = new class implements FirebaseMessagingClient
    {
        public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
        {
            return [
                'successes' => 0,
                'failures' => count($tokens),
                'invalid_tokens' => ['invalid-token'],
            ];
        }

        public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
        {
            return [
                'successes' => 1,
                'failures' => 0,
                'invalid_tokens' => [],
            ];
        }
    };

    app(SendPushNotificationJob::class, ['notificationId' => $notification->id])->handle($fakeClient);

    $recipient->refresh();

    expect($recipient->status)->toBe(NotificationDeliveryStatus::Failed);

    $this->assertDatabaseHas('device_tokens', [
        'user_id' => $user->id,
        'fcm_token' => 'invalid-token',
        'is_active' => false,
    ]);
});