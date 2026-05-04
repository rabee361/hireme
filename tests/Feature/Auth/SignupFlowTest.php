<?php

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