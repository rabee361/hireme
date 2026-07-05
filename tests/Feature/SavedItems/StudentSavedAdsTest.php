<?php

use App\Models\Ad;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('allows a student to save an ad', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();

    $ad = Ad::query()->create([
        'job_name' => 'Laravel Developer',
        'req1' => 'PHP',
        'task1' => 'Build APIs',
        'feature1' => 'Remote',
        'github_required' => true,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)
        ->postJson('/api/student/saved-ads/'.$ad->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Ad saved successfully.');

    $this->assertDatabaseHas('student_saved_ads', [
        'student_id' => $student->id,
        'ad_id' => $ad->id,
    ]);
});

it('returns a specific message when saving an already-saved ad', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();

    $ad = Ad::query()->create([
        'job_name' => 'Backend Engineer',
        'req1' => 'PHP',
        'task1' => 'Build APIs',
        'feature1' => 'Remote',
        'github_required' => false,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    Student::findOrFail($student->id)->savedAds()->attach($ad->id);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)
        ->postJson('/api/student/saved-ads/'.$ad->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Ad is already in your saved list.');
});

it('lists saved ads for the authenticated student', function (): void {
    $company = User::factory()->company()->create([
        'username' => 'TechCorp',
        'description' => 'A tech company.',
        'avatar' => 'companies/tech.png',
    ]);

    $student = User::factory()->student()->create();

    $ad = Ad::query()->create([
        'job_name' => 'Laravel Developer',
        'req1' => 'PHP',
        'task1' => 'Build APIs',
        'feature1' => 'Remote',
        'github_required' => true,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    Student::findOrFail($student->id)->savedAds()->attach($ad->id);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)->getJson('/api/student/saved-ads');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Saved ads retrieved successfully.')
        ->assertJsonCount(1, 'data');

    expect($response->json('data.0.job_name'))->toBe('Laravel Developer');
    expect($response->json('data.0.applicants_count'))->toBe(0);
    expect($response->json('data.0.company.name'))->toBe('TechCorp');
    expect($response->json('data.0.company.description'))->toBe('A tech company.');
});

it('allows a student to remove a saved ad', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();

    $ad = Ad::query()->create([
        'job_name' => 'DevOps Engineer',
        'req1' => 'Docker',
        'task1' => 'CI/CD',
        'feature1' => 'Remote',
        'github_required' => false,
        'resume_required' => false,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    Student::findOrFail($student->id)->savedAds()->attach($ad->id);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)
        ->deleteJson('/api/student/saved-ads/'.$ad->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Ad removed from saved list successfully.');

    $this->assertDatabaseMissing('student_saved_ads', [
        'student_id' => $student->id,
        'ad_id' => $ad->id,
    ]);
});

it('returns a specific message when removing an ad not in saved list', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();

    $ad = Ad::query()->create([
        'job_name' => 'Data Scientist',
        'req1' => 'Python',
        'task1' => 'ML models',
        'feature1' => 'Remote',
        'github_required' => false,
        'resume_required' => false,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)
        ->deleteJson('/api/student/saved-ads/'.$ad->id);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Ad was not in your saved list.');
});

it('prevents a non-student user from saving an ad', function (): void {
    $company = User::factory()->company()->create();
    $ad = Ad::query()->create([
        'job_name' => 'Test Ad',
        'req1' => 'Req',
        'task1' => 'Task',
        'feature1' => 'Feature',
        'github_required' => false,
        'resume_required' => false,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $this->withToken($token)
        ->postJson('/api/student/saved-ads/'.$ad->id)
        ->assertForbidden();
});
