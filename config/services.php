<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL', 'https://rabeehasan1028.app.n8n.cloud/webhook/30657344-665f-4bb8-a7ad-a8fa5f87c38f'),
    ],

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID', 'hiremee-placeholder-project'),
        'credentials' => (static function (): string {
            $credentials = env('FIREBASE_CREDENTIALS');

            if (is_string($credentials) && $credentials !== '') {
                return preg_match('#^(?:[A-Za-z]:[\\\\/]|[\\\\/])#', $credentials) === 1
                    ? $credentials
                    : base_path($credentials);
            }

            return storage_path('app/firebase/firebase-credentials.placeholder.json');
        })(),
    ],

];
