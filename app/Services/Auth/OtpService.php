<?php

namespace App\Services\Auth;

use App\Enums\OtpType;
use App\Mail\AuthOtpMail;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    public function issue(User $user, OtpType $type): Otp
    {
        $this->invalidateActiveOtps($user, $type);

        return $user->otps()->create([
            'type' => $type,
            'code' => Str::upper(Str::random(6)),
            'expires_at' => now()->addMinutes(15),
            'is_used' => false,
        ]);
    }

    public function deliver(User $user, Otp $otp): void
    {
        Mail::to($user->email)->send(new AuthOtpMail($otp));
    }

    public function verify(User $user, OtpType $type, string $code): ?Otp
    {
        $otp = $user->otps()
            ->where('type', $type)
            ->where('code', Str::upper($code))
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (! $otp) {
            return null;
        }

        $otp->forceFill([
            'is_used' => true,
        ])->save();

        return $otp;
    }

    public function invalidateActiveOtps(User $user, OtpType $type): void
    {
        $user->otps()
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->update([
                'is_used' => true,
            ]);
    }
}