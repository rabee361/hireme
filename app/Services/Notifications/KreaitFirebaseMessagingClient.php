<?php

namespace App\Services\Notifications;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class KreaitFirebaseMessagingClient implements FirebaseMessagingClient
{
    public function __construct(
        private readonly Messaging $messaging,
    ) {
    }

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($this->normalizeData($data));

        $report = $this->messaging->sendMulticast($message, array_values($tokens));

        return [
            'successes' => $report->successes()->count(),
            'failures' => $report->failures()->count(),
            'invalid_tokens' => $report->unknownTokens(),
        ];
    }

    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(Notification::create($title, $body))
            ->withData($this->normalizeData($data));

        $this->messaging->send($message);

        return [
            'successes' => 1,
            'failures' => 0,
            'invalid_tokens' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function normalizeData(array $data): array
    {
        return collect($data)
            ->mapWithKeys(fn ($value, $key): array => [(string) $key => is_scalar($value) ? (string) $value : json_encode($value, JSON_THROW_ON_ERROR)])
            ->all();
    }
}