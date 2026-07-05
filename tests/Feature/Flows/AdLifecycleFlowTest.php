<?php

use App\Models\Ad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('completes the full ad lifecycle from creation to application review', function (): void {
    // ── Act 1: Company A authenticates and creates a new Ad ──
    $company = User::factory()->company()->create([
        'username' => 'TechCorp',
        'description' => 'A leading tech company.',
        'avatar' => 'companies/tech.png',
    ]);

    $companyToken = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $createAdResponse = $this->withToken($companyToken)->postJson('/api/ads', [
        'job_name' => 'Senior Laravel Developer',
        'req1' => 'PHP',
        'task1' => 'Build and maintain APIs',
        'feature1' => 'Remote work',
        'github_required' => true,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => true,
    ]);

    $createAdResponse
        ->assertCreated()
        ->assertJsonPath('data.job_name', 'Senior Laravel Developer')
        ->assertJsonPath('data.company_id', $company->id);

    $adId = $createAdResponse->json('data.id');

    // ── Act 2: Student B authenticates, views ads, and saves the ad ──
    $student = User::factory()->student()->create([
        'username' => 'JaneDev',
    ]);
    $student->studentProfile()->create([
        'title' => 'Backend Developer',
        'address' => 'Cairo',
        'hour_cost' => 60,
        'experience_years' => 4,
    ]);

    $studentToken = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    // Student views ads list
    $this->withToken($studentToken)
        ->getJson('/api/ads')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.job_name', 'Senior Laravel Developer');

    // Student saves the ad
    $saveAdResponse = $this->withToken($studentToken)
        ->postJson('/api/student/saved-ads/'.$adId);

    // Debug
    if (! $saveAdResponse->isOk()) {
        file_put_contents(__DIR__ . '/debug.txt', $saveAdResponse->getContent() . PHP_EOL, FILE_APPEND);
    }

    $saveAdResponse
        ->assertOk()
        ->assertJsonPath('message', 'Ad saved successfully.');

    $this->assertDatabaseHas('student_saved_ads', [
        'student_id' => $student->id,
        'ad_id' => $adId,
    ]);

    // ── Act 3: Student B applies to the Ad ──
    $applyResponse = $this->withToken($studentToken)->postJson('/api/ad-applications', [
        'ad_id' => $adId,
        'github_link' => 'https://github.com/janedev',
        'expected_salary' => 2000,
        'resume' => 'https://resumes.com/janedev.pdf',
    ]);

    $applyResponse
        ->assertCreated()
        ->assertJsonPath('message', 'Ad application submitted successfully.');

    $applicationId = $applyResponse->json('data.id');

    // ── Act 4: Company A calls the application list API ──
    $applicationsResponse = $this->withToken($companyToken)
        ->getJson('/api/ads/'.$adId.'/applications');

    $applicationsResponse
        ->assertOk()
        ->assertJsonPath('message', 'Ad applications retrieved successfully.')
        ->assertJsonCount(1, 'data');

    // ── Assert: The response contains Student B's application, with timestamps ──
    $applicationData = $applicationsResponse->json('data.0');

    expect($applicationData['id'])->toBe($applicationId);
    expect($applicationData['student']['username'])->toBe('JaneDev');
    expect($applicationData['github_link'])->toBe('https://github.com/janedev');
    expect($applicationData['expected_salary'])->toBe(2000);
    expect($applicationData['resume'])->toBe('https://resumes.com/janedev.pdf');

    // Verify created_at timestamp is present and valid
    expect($applicationData['created_at'])->not->toBeNull();
    expect($applicationData['updated_at'])->not->toBeNull();
});
