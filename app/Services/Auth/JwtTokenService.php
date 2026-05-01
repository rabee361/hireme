<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JwtTokenService
{
    public function issueTokenPair(User $user): array
    {
        $accessTtl = $this->accessTtl();
        $refreshTtl = $this->refreshTtl();

        $accessToken = auth('api')
            ->claims(['token_type' => 'access'])
            ->setTTL($accessTtl)
            ->fromUser($user);

        $refreshToken = auth('api')
            ->claims(['token_type' => 'refresh'])
            ->setTTL($refreshTtl)
            ->fromUser($user);

        return [
            'access_token' => $accessToken,
            'access_token_expires_at' => now()->addMinutes($accessTtl)->toISOString(),
            'refresh_token' => $refreshToken,
            'refresh_token_expires_at' => now()->addMinutes($refreshTtl)->toISOString(),
            'token_type' => 'Bearer',
        ];
    }

    public function refresh(string $refreshToken): array
    {
        try {
            $payload = JWTAuth::setToken($refreshToken)->getPayload();
            $user = JWTAuth::setToken($refreshToken)->authenticate();
        } catch (JWTException) {
            throw ValidationException::withMessages([
                'refresh_token' => ['The refresh token is invalid or expired.'],
            ]);
        }

        if (! $user) {
            throw ValidationException::withMessages([
                'refresh_token' => ['The refresh token is invalid or expired.'],
            ]);
        }

        if ($payload->get('token_type') !== 'refresh') {
            throw ValidationException::withMessages([
                'refresh_token' => ['The provided token is not a refresh token.'],
            ]);
        }

        if ((int) $payload->get('token_version', -1) !== (int) $user->token_version) {
            throw ValidationException::withMessages([
                'refresh_token' => ['The refresh token is no longer valid.'],
            ]);
        }

        JWTAuth::setToken($refreshToken)->invalidate();

        return $this->issueTokenPair($user);
    }

    public function accessTtl(): int
    {
        return 60 * 24;
    }

    public function refreshTtl(): int
    {
        return 60 * 24 * 7;
    }
}