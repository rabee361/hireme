<?php

use App\Models\Company;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('allows a company to save a student', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $response = $this->withToken($token)
        ->postJson('/api/saved-students/'.$student->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Student saved successfully.');

    $this->assertDatabaseHas('company_saved_students', [
        'company_id' => $company->id,
        'student_id' => $student->id,
    ]);
});

it('returns a specific message when saving an already-saved student', function (): void {
    $companyUser = User::factory()->company()->create();
    $student = User::factory()->student()->create();

    /** @var Company $company */
    $company = Company::findOrFail($companyUser->id);
    $company->savedStudents()->attach($student->id);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $response = $this->withToken($token)
        ->postJson('/api/saved-students/'.$student->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Student is already in your saved list.');
});

it('lists saved students for the authenticated company', function (): void {
    $company = User::factory()->company()->create();
    $studentA = User::factory()->student()->create([
        'username' => 'Alice',
        'description' => 'Frontend dev',
    ]);
    $studentA->studentProfile()->create([
        'title' => 'Frontend Developer',
        'address' => 'Cairo',
    ]);

    $studentB = User::factory()->student()->create([
        'username' => 'Bob',
        'description' => 'Backend dev',
    ]);
    $studentB->studentProfile()->create([
        'title' => 'Backend Developer',
        'address' => 'Alexandria',
    ]);

    /** @var Company $company */
    $companyModel = Company::findOrFail($company->id);
    $companyModel->savedStudents()->attach([$studentA->id, $studentB->id]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $response = $this->withToken($token)->getJson('/api/saved-students');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Saved students retrieved successfully.')
        ->assertJsonCount(2, 'data');

    expect($response->json('data'))->toHaveCount(2);
    expect($response->json('data.0.username'))->toBe('Alice');
    expect($response->json('data.1.username'))->toBe('Bob');
});

it('includes the student profile in the saved students list', function (): void {
    $companyUser = User::factory()->company()->create();
    /** @var Company $company */
    $company = Company::findOrFail($companyUser->id);
    $student = User::factory()->student()->create(['username' => 'ProfileStudent']);
    $student->studentProfile()->create([
        'title' => 'Full Stack Developer',
        'hour_cost' => 60,
    ]);

    $company->savedStudents()->attach($student->id);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $response = $this->withToken($token)->getJson('/api/saved-students');

    $response->assertOk();
    expect($response->json('data.0.profile.title'))->toBe('Full Stack Developer');
    expect($response->json('data.0.profile.hour_cost'))->toBe(60);
});

it('allows a company to remove a saved student', function (): void {
    $companyUser = User::factory()->company()->create();
    /** @var Company $company */
    $company = Company::findOrFail($companyUser->id);
    $student = User::factory()->student()->create();

    $company->savedStudents()->attach($student->id);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $response = $this->withToken($token)
        ->deleteJson('/api/saved-students/'.$student->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Student removed from saved list successfully.');

    $this->assertDatabaseMissing('company_saved_students', [
        'company_id' => $company->id,
        'student_id' => $student->id,
    ]);
});

it('returns a specific message when removing a student not in saved list', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $response = $this->withToken($token)
        ->deleteJson('/api/saved-students/'.$student->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Student was not in your saved list.');
});

it('prevents a non-company user from saving a student', function (): void {
    $student = User::factory()->student()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $this->withToken($token)
        ->postJson('/api/saved-students/'.$student->id)
        ->assertForbidden();
});
