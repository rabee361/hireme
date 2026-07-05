<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

// ── Search / List ─────────────────────────────────────────────

it('lists students with optional name filter', function (): void {
    $company = User::factory()->company()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    User::factory()->student()->create(['username' => 'Alice Johnson']);
    User::factory()->student()->create(['username' => 'Bob Smith']);
    User::factory()->student()->create(['username' => 'Charlie Brown']);

    // Without filter
    $response = $this->withToken($token)->getJson('/api/students');
    $response->assertOk()->assertJsonCount(3, 'data');

    // With name filter
    $filtered = $this->withToken($token)->getJson('/api/students?name=Ali');
    $filtered->assertOk()->assertJsonCount(1, 'data');
    expect($filtered->json('data.0.username'))->toBe('Alice Johnson');
});

it('lists companies with optional name filter', function (): void {
    $company = User::factory()->company()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    User::factory()->company()->create(['username' => 'Acme Corp']);
    User::factory()->company()->create(['username' => 'Beta Inc']);

    $response = $this->withToken($token)->getJson('/api/companies');
    // $company + 2 created = 3
    $response->assertOk()->assertJsonCount(3, 'data');

    $filtered = $this->withToken($token)->getJson('/api/companies?name=acme');
    $filtered->assertOk()->assertJsonCount(1, 'data');
    expect($filtered->json('data.0.username'))->toBe('Acme Corp');
});

it('lists customers with optional name filter', function (): void {
    $customer = User::factory()->customer()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($customer);

    User::factory()->customer()->create(['username' => 'Customer One']);

    $response = $this->withToken($token)->getJson('/api/customers');
    // $customer + 1 created = 2
    $response->assertOk()->assertJsonCount(2, 'data');

    $filtered = $this->withToken($token)->getJson('/api/customers?name=One');
    $filtered->assertOk()->assertJsonCount(1, 'data');
    expect($filtered->json('data.0.username'))->toBe('Customer One');
});

// ── Show ──────────────────────────────────────────────────────

it('shows a single student with profile', function (): void {
    $company = User::factory()->company()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $student = User::factory()->student()->create([
        'username' => 'ShowStudent',
        'description' => 'A test student.',
    ]);
    $student->studentProfile()->create([
        'title' => 'Backend Dev',
        'address' => 'Cairo',
        'hour_cost' => 40,
        'experience_years' => 3,
    ]);

    $response = $this->withToken($token)->getJson('/api/students/'.$student->id);

    $response
        ->assertOk()
        ->assertJsonPath('data.username', 'ShowStudent')
        ->assertJsonPath('data.description', 'A test student.')
        ->assertJsonStructure([
            'data' => [
                'id', 'username', 'email', 'type', 'profile' => [
                    'id', 'user_id', 'title', 'address', 'hour_cost', 'experience_years',
                ],
            ],
        ]);
    expect($response->json('data.type'))->toBe('student');
    expect($response->json('data.profile.title'))->toBe('Backend Dev');
});

// ── Profile Update ────────────────────────────────────────────

it('allows a student to update their own profile', function (): void {
    $user = User::factory()->student()->create([
        'username' => 'old-username',
        'phone_number' => null,
    ]);
    $user->studentProfile()->create([
        'title' => 'Junior Dev',
        'address' => 'Giza',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($user);

    $response = $this->withToken($token)->patchJson('/api/students/'.$user->id, [
        'username' => 'new-username',
        'phone_number' => '+201234567890',
        'title' => 'Senior Dev',
        'address' => 'Cairo',
        'hour_cost' => 80,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Student profile updated successfully.')
        ->assertJsonPath('data.username', 'new-username')
        ->assertJsonPath('data.phone_number', '+201234567890')
        ->assertJsonPath('data.profile.title', 'Senior Dev')
        ->assertJsonPath('data.profile.address', 'Cairo');

    // hour_cost is decimal; accept numeric match
    expect((float) $response->json('data.profile.hour_cost'))->toBe(80.0);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'username' => 'new-username',
        'phone_number' => '+201234567890',
    ]);
    $this->assertDatabaseHas('student_profiles', [
        'user_id' => $user->id,
        'title' => 'Senior Dev',
        'address' => 'Cairo',
        'hour_cost' => 80.00,
    ]);
});

it('prevents a student from updating another students profile', function (): void {
    $owner = User::factory()->student()->create(['username' => 'owner']);
    $owner->studentProfile()->create();

    $intruder = User::factory()->student()->create(['username' => 'intruder']);
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($intruder);

    $this->withToken($token)
        ->patchJson('/api/students/'.$owner->id, [
            'username' => 'hacked',
        ])
        ->assertForbidden();
});

it('allows a company to update their own profile', function (): void {
    $user = User::factory()->company()->create([
        'username' => 'old-company',
        'description' => null,
    ]);
    $user->companyProfile()->create([
        'employees_count' => 10,
        'started_at' => '2023-01-01',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($user);

    $response = $this->withToken($token)->patchJson('/api/companies/'.$user->id, [
        'username' => 'Updated Corp',
        'description' => 'A great company.',
        'employees_count' => 50,
        'started_at' => '2020-06-15',
        'tech1' => 'Laravel',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Company profile updated successfully.')
        ->assertJsonPath('data.username', 'Updated Corp')
        ->assertJsonPath('data.description', 'A great company.')
        ->assertJsonPath('data.profile.employees_count', 50);

    // In SQLite date columns store as Y-m-d H:i:s; expect the full format
    $this->assertDatabaseHas('company_profiles', [
        'user_id' => $user->id,
        'employees_count' => 50,
        'tech1' => 'Laravel',
    ]);
});

it('prevents a company from updating another companys profile', function (): void {
    $owner = User::factory()->company()->create();
    $owner->companyProfile()->create();

    $intruder = User::factory()->company()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($intruder);

    $this->withToken($token)
        ->patchJson('/api/companies/'.$owner->id, [
            'username' => 'hacked',
        ])
        ->assertForbidden();
});

it('allows a customer to update their own profile', function (): void {
    $user = User::factory()->customer()->create([
        'username' => 'old-customer',
    ]);
    $user->customerProfile()->create([
        'title' => 'Client',
        'address' => 'Giza',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($user);

    $response = $this->withToken($token)->patchJson('/api/customers/'.$user->id, [
        'username' => 'Updated Customer',
        'title' => 'Premium Client',
        'address' => 'Alexandria',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Customer profile updated successfully.')
        ->assertJsonPath('data.username', 'Updated Customer')
        ->assertJsonPath('data.profile.title', 'Premium Client');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'username' => 'Updated Customer',
    ]);
    $this->assertDatabaseHas('customer_profiles', [
        'user_id' => $user->id,
        'title' => 'Premium Client',
        'address' => 'Alexandria',
    ]);
});

it('prevents a customer from updating another customers profile', function (): void {
    $owner = User::factory()->customer()->create();
    $owner->customerProfile()->create();

    $intruder = User::factory()->customer()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($intruder);

    $this->withToken($token)
        ->patchJson('/api/customers/'.$owner->id, [
            'username' => 'hacked',
        ])
        ->assertForbidden();
});
