<?php

namespace App\Services\Notifications;

interface FirebaseMessagingClient
{
    /**
     * @param  array<string, string>  $data
     * @return array<string, mixed>
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array;

    /**
     * @param  array<string, string>  $data
     * @return array<string, mixed>
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array;
}