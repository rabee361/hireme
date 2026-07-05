<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('completes the company scouting flow from search to saved list', function (): void {
    // ── Act 1: Company authenticates and searches for a student ──
    $company = User::factory()->company()->create([
        'username' => 'RecruitCorp',
    ]);

    // Create several students, one of which matches the search
    User::factory()->student()->create(['username' => 'Alice Cooper']);
    User::factory()->student()->create(['username' => 'Bob Smith']);
    $targetStudent = User::factory()->student()->create(['username' => 'John Developer']);
    $targetStudent->studentProfile()->create([
        'title' => 'Senior Backend Developer',
        'address' => 'Cairo',
        'hour_cost' => 80,
        'experience_years' => 6,
        'tech1' => 'Laravel',
        'tech2' => 'Vue',
        'tech3' => 'MySQL',
    ]);

    $companyToken = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $searchResponse = $this->withToken($companyToken)->getJson('/api/students?name=John');

    $searchResponse
        ->assertOk()
        ->assertJsonCount(1, 'data');

    expect($searchResponse->json('data.0.username'))->toBe('John Developer');
    expect($searchResponse->json('data.0.profile.title'))->toBe('Senior Backend Developer');

    // ── Act 2: Company views the student's detailed profile ──
    $profileResponse = $this->withToken($companyToken)
        ->getJson('/api/students/'.$targetStudent->id);

    $profileResponse
        ->assertOk()
        ->assertJsonPath('message', 'Student retrieved successfully.')
        ->assertJsonPath('data.username', 'John Developer')
        ->assertJsonPath('data.profile.title', 'Senior Backend Developer')
        ->assertJsonPath('data.profile.hour_cost', 80)
        ->assertJsonPath('data.profile.experience_years', 6);

    // ── Act 3: Company saves the student for later ──
    $saveResponse = $this->withToken($companyToken)
        ->postJson('/api/saved-students/'.$targetStudent->id);

    $saveResponse
        ->assertOk()
        ->assertJsonPath('message', 'Student saved successfully.');

    $this->assertDatabaseHas('company_saved_students', [
        'company_id' => $company->id,
        'student_id' => $targetStudent->id,
    ]);

    // ── Act 4: Company retrieves their saved students list ──
    $savedListResponse = $this->withToken($companyToken)
        ->getJson('/api/saved-students');

    $savedListResponse
        ->assertOk()
        ->assertJsonPath('message', 'Saved students retrieved successfully.')
        ->assertJsonCount(1, 'data');

    // ── Assert: The searched student appears accurately in the saved list ──
    expect($savedListResponse->json('data.0.username'))->toBe('John Developer');
    expect($savedListResponse->json('data.0.profile.title'))->toBe('Senior Backend Developer');
    expect($savedListResponse->json('data.0.profile.hour_cost'))->toBe(80);
});
