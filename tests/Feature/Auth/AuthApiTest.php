<?php

use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('fails login with bad credentials', function (): void {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => Hash::make('correct-password'),
        'is_verified' => true,
        'email_verified_at' => now(),
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'john@example.com',
        'password' => 'wrong-password',
    ])
        ->assertStatus(422)
        ->assertJsonPath('message', 'The provided credentials are incorrect.');
});

it('fails login when email is not verified', function (): void {
    User::factory()->unverified()->create([
        'email' => 'unverified@example.com',
    ]);

    $this->postJson('/api/auth/login', [
        'email' => 'unverified@example.com',
        'password' => 'password',
    ])
        ->assertForbidden()
        ->assertJsonPath('message', 'Your email address must be verified before logging in.');
});

it('succeeds login and returns JWT tokens and user payload', function (): void {
    $user = User::factory()->create([
        'email' => 'valid@example.com',
        'is_verified' => true,
        'email_verified_at' => now(),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'valid@example.com',
        'password' => 'password',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Logged in successfully.')
        ->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'access_token_expires_at',
                'refresh_token',
                'refresh_token_expires_at',
                'token_type',
                'user' => [
                    'id',
                    'username',
                    'email',
                    'is_verified',
                    'type',
                ],
            ],
        ]);

    expect($response->json('data.token_type'))->toBe('Bearer');
    expect($response->json('data.user.id'))->toBe($user->id);
    expect($response->json('data.user.email'))->toBe('valid@example.com');
});

it('logs out and invalidates the JWT', function (): void {
    $user = User::factory()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($user);

    $originalVersion = $user->token_version;

    $this->withToken($token)
        ->postJson('/api/auth/logout')
        ->assertOk()
        ->assertJsonPath('message', 'Logged out successfully.');

    $user->refresh();

    expect($user->token_version)->toBe($originalVersion + 1);
});

it('refreshes the JWT using a valid refresh token', function (): void {
    $user = User::factory()->create();
    $refreshToken = auth('api')
        ->claims(['token_type' => 'refresh'])
        ->setTTL(60 * 24 * 7)
        ->fromUser($user);

    $response = $this->postJson('/api/auth/refresh', [
        'refresh_token' => $refreshToken,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Token refreshed successfully.')
        ->assertJsonStructure([
            'message',
            'data' => [
                'access_token',
                'access_token_expires_at',
                'refresh_token',
                'refresh_token_expires_at',
                'token_type',
            ],
        ]);
});

it('rejects an invalid refresh token', function (): void {
    $this->postJson('/api/auth/refresh', [
        'refresh_token' => 'invalid-token',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['refresh_token']);
});

it('rejects an expired token_version refresh token', function (): void {
    $user = User::factory()->create();

    $refreshToken = auth('api')
        ->claims(['token_type' => 'refresh'])
        ->setTTL(60 * 24 * 7)
        ->fromUser($user);

    // Increment token_version to invalidate the token
    $user->forceFill(['token_version' => $user->token_version + 1])->save();

    $this->postJson('/api/auth/refresh', [
        'refresh_token' => $refreshToken,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['refresh_token']);
});

it('sends an OTP for password reset via send-email', function (): void {
    $user = User::factory()->create([
        'email' => 'reset@example.com',
    ]);

    $response = $this->postJson('/api/auth/send-email', [
        'email' => 'reset@example.com',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'If the email exists, an OTP has been sent.')
        ->assertJsonPath('data.type', 'password_reset');

    $this->assertDatabaseHas('otps', [
        'user_id' => $user->id,
        'type' => 'password_reset',
        'is_used' => false,
    ]);
});

it('changes password using a valid reset token', function (): void {
    $user = User::factory()->create([
        'email' => 'changepw@example.com',
    ]);

    // Create a password reset token via Password broker
    $resetToken = Password::broker()->createToken($user);

    $response = $this->postJson('/api/auth/change-password', [
        'email' => 'changepw@example.com',
        'token' => $resetToken,
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Password changed successfully.');

    $user->refresh();

    expect(Hash::check('NewPassword123!', $user->password))->toBeTrue();
});

it('rejects change-password with an invalid reset token', function (): void {
    User::factory()->create([
        'email' => 'invalidtoken@example.com',
    ]);

    $this->postJson('/api/auth/change-password', [
        'email' => 'invalidtoken@example.com',
        'token' => 'bogus-token',
        'password' => 'NewPassword123!',
        'password_confirmation' => 'NewPassword123!',
    ])
        ->assertStatus(422)
        ->assertJsonPath('message', 'The password reset token is invalid or expired.');
});
