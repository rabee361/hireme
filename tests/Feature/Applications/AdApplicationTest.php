<?php

use App\Models\Ad;
use App\Models\AdApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('allows a student to apply to an ad', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();
    $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

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

    $response = $this->withToken($token)->postJson('/api/ad-applications', [
        'ad_id' => $ad->id,
        'github_link' => 'https://github.com/testuser',
        'resume' => 'https://resumes.com/testuser.pdf',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Ad application submitted successfully.')
        ->assertJsonStructure([
            'message',
            'data' => [
                'id', 'ad_id', 'student_profile_id',
                'github_link', 'resume', 'created_at', 'updated_at', 'student', 'ad',
            ],
        ]);

    expect($response->json('data.github_link'))->toBe('https://github.com/testuser');
    expect($response->json('data.resume'))->toBe('https://resumes.com/testuser.pdf');
});

it('prevents a student from applying twice to the same ad', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

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

    // Create first application
    $profile->adApplications()->create([
        'ad_id' => $ad->id,
        'github_link' => 'https://github.com/testuser',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $this->withToken($token)
        ->postJson('/api/ad-applications', [
            'ad_id' => $ad->id,
            'github_link' => 'https://github.com/testuser',
            'resume' => 'resume.pdf',
        ])
        ->assertStatus(409)
        ->assertJsonPath('message', 'You have already applied to this ad.');
});

it('allows a student to update their ad application', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

    $ad = Ad::query()->create([
        'job_name' => 'Laravel Developer',
        'req1' => 'PHP',
        'task1' => 'Build APIs',
        'feature1' => 'Remote',
        'github_required' => true,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => true,
        'company_id' => $company->id,
    ]);

    $application = $profile->adApplications()->create([
        'ad_id' => $ad->id,
        'github_link' => 'https://github.com/old',
        'expected_salary' => 500,
        'resume' => 'old-resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)->patchJson('/api/ad-applications/'.$application->id, [
        'github_link' => 'https://github.com/updated',
        'expected_salary' => 800,
        'resume' => 'updated-resume.pdf',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Ad application updated successfully.')
        ->assertJsonPath('data.github_link', 'https://github.com/updated')
        ->assertJsonPath('data.expected_salary', 800)
        ->assertJsonPath('data.resume', 'updated-resume.pdf');
});

it('allows a student to delete (withdraw) their ad application', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create();
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

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

    $application = $profile->adApplications()->create([
        'ad_id' => $ad->id,
        'github_link' => 'https://github.com/testuser',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $this->withToken($token)
        ->deleteJson('/api/ad-applications/'.$application->id)
        ->assertOk()
        ->assertJsonPath('message', 'Ad application deleted successfully.');

    $this->assertDatabaseMissing('ad_applications', ['id' => $application->id]);
});

it('allows a student to view their ad applications', function (): void {
    $company = User::factory()->company()->create([
        'username' => 'TechCorp',
        'description' => 'A tech company.',
    ]);
    $student = User::factory()->student()->create(['username' => 'StudentDev']);
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

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

    $profile->adApplications()->create([
        'ad_id' => $ad->id,
        'github_link' => 'https://github.com/testuser',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)->getJson('/api/student/my-ad-applications');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'My ad applications retrieved successfully.')
        ->assertJsonCount(1, 'data');

    expect($response->json('data.0.ad.job_name'))->toBe('Laravel Developer');
    expect($response->json('data.0.student.username'))->toBe('StudentDev');
    expect($response->json('data.0.ad.company.name'))->toBe('TechCorp');
});

it('allows a company to view applications for their ad', function (): void {
    $company = User::factory()->company()->create();
    $student = User::factory()->student()->create(['username' => 'ApplicantOne']);
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

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

    $profile->adApplications()->create([
        'ad_id' => $ad->id,
        'github_link' => 'https://github.com/applicant',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $response = $this->withToken($token)->getJson('/api/ads/'.$ad->id.'/applications');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Ad applications retrieved successfully.')
        ->assertJsonCount(1, 'data');

    expect($response->json('data.0.student.username'))->toBe('ApplicantOne');
    expect($response->json('data.0.github_link'))->toBe('https://github.com/applicant');
    expect($response->json('data.0.ad_id'))->toBe($ad->id);
});

it('prevents a company from viewing applications for another companys ad', function (): void {
    $owner = User::factory()->company()->create();
    $intruder = User::factory()->company()->create();

    $ad = Ad::query()->create([
        'job_name' => 'Secret Ad',
        'req1' => 'PHP',
        'task1' => 'Build APIs',
        'feature1' => 'Remote',
        'github_required' => false,
        'resume_required' => false,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $owner->id,
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($intruder);

    $this->withToken($token)
        ->getJson('/api/ads/'.$ad->id.'/applications')
        ->assertForbidden();
});
