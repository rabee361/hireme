<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class N8nEmailService
{
    private string $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = config('services.n8n.webhook_url');
    }

    public function sendOtpEmail(string $otpCode, string $email): bool
    {
        $subject = 'رمز التحقق من بريدك الإلكتروني';
        $htmlBody = View::make('emails.auth.otp-n8n', [
            'otp_code' => $otpCode,
        ])->render();

        return $this->send($email, $subject, $htmlBody);
    }

    private function send(string $email, string $subject, string $body): bool
    {
        try {
            $response = Http::withoutVerifying()->post($this->webhookUrl, [
                'email' => $email,
                'subject' => $subject,
                'body' => $body,
            ]);

            $response->throw();

            return true;
        } catch (\Exception $e) {
            Log::error("Error sending email via n8n: {$e->getMessage()}", [
                'email' => $email,
                'subject' => $subject,
            ]);

            return false;
        }
    }
}
