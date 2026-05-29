<?php

use App\Models\DeviceToken;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret');
    Mail::fake();
});

it('creates a linked profile for each supported signup type', function (string $userType, string $profileTable): void {
    $response = $this->postJson('/api/auth/signup', [
        'username' => 'user_'.$userType,
        'email' => $userType.'@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'user_type' => $userType,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.email', $userType.'@example.com')
        ->assertJsonPath('data.type', 'signup');

    $user = User::query()->where('email', $userType.'@example.com')->firstOrFail();

    expect($user->is_verified)->toBeFalse();
    expect($user->email_verified_at)->toBeNull();

    $this->assertDatabaseHas($profileTable, [
        'user_id' => $user->id,
    ]);
})->with([
    ['student', 'student_profiles'],
    ['customer', 'customer_profiles'],
    ['company', 'company_profiles'],
]);

it('marks the user as verified in both verification fields after signup otp verification', function (): void {
    $signupResponse = $this->postJson('/api/auth/signup', [
        'username' => 'verify_user',
        'email' => 'verify@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'user_type' => 'student',
    ]);

    $signupResponse->assertCreated();

    $user = User::query()->where('email', 'verify@example.com')->firstOrFail();
    $otp = Otp::query()->where('user_id', $user->id)->where('type', 'signup')->latest('created_at')->firstOrFail();

    $this->postJson('/api/auth/verify-otp', [
        'email' => $user->email,
        'code' => $otp->code,
        'type' => 'signup',
    ])->assertOk();

    $user->refresh();

    expect($user->is_verified)->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();
});

it('stores a device token after signup otp verification when device data is provided', function (): void {
    $this->postJson('/api/auth/signup', [
        'username' => 'device_verify_user',
        'email' => 'device-verify@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'user_type' => 'student',
    ])->assertCreated();

    $user = User::query()->where('email', 'device-verify@example.com')->firstOrFail();
    $otp = Otp::query()->where('user_id', $user->id)->where('type', 'signup')->latest('created_at')->firstOrFail();

    $this->postJson('/api/auth/verify-otp', [
        'email' => $user->email,
        'code' => $otp->code,
        'type' => 'signup',
        'device_id' => 'android-1',
        'fcm_token' => 'token-verify-1',
        'platform' => 'android',
        'app_version' => '1.0.0',
    ])->assertOk();

    $this->assertDatabaseHas('device_tokens', [
        'user_id' => $user->id,
        'device_id' => 'android-1',
        'fcm_token' => 'token-verify-1',
        'platform' => 'android',
        'is_active' => true,
    ]);
});

it('requires is_verified as well as email_verified_at for login', function (): void {
    $user = User::factory()->create([
        'email' => 'login@example.com',
        'is_verified' => false,
        'email_verified_at' => now(),
    ]);

    $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertForbidden()
        ->assertJsonPath('message', 'Your email address must be verified before logging in.');
});

it('stores or updates a device token on login when device data is provided', function (): void {
    $user = User::factory()->create([
        'email' => 'device-login@example.com',
        'password' => 'Password123!',
        'is_verified' => true,
        'email_verified_at' => now(),
    ]);

    DeviceToken::query()->create([
        'user_id' => $user->id,
        'device_id' => 'android-1',
        'fcm_token' => 'old-token',
        'platform' => 'android',
        'app_version' => '1.0.0',
        'is_active' => true,
        'last_seen_at' => now()->subDay(),
    ]);

    $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'Password123!',
        'device_id' => 'android-1',
        'fcm_token' => 'new-token',
        'platform' => 'android',
        'app_version' => '2.0.0',
    ])
        ->assertOk()
        ->assertJsonPath('message', 'Logged in successfully.');

    $this->assertDatabaseHas('device_tokens', [
        'user_id' => $user->id,
        'device_id' => 'android-1',
        'fcm_token' => 'new-token',
        'platform' => 'android',
        'app_version' => '2.0.0',
        'is_active' => true,
    ]);
});