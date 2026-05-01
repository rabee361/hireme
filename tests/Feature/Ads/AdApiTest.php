<?php

use App\Models\Ad;
use App\Models\AdApplication;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret');
});

it('lists ads with applicant count company details and since field', function (): void {
    $company = User::factory()->company()->create([
        'username' => 'Acme Labs',
        'description' => 'Product engineering company.',
        'avatar' => 'companies/acme.png',
    ]);

    $student = User::factory()->student()->create();

    $studentProfile = StudentProfile::query()->create([
        'user_id' => $student->id,
        'address' => 'Cairo',
        'hour_cost' => 50,
        'experience_years' => 2,
        'college' => 'Ain Shams',
        'title' => 'Frontend Developer',
    ]);

    $ad = Ad::query()->create([
        'job_name' => 'Laravel Developer',
        'req1' => 'PHP',
        'task1' => 'Build APIs',
        'feature1' => 'Remote work',
        'github_required' => true,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    $ad->forceFill([
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ])->save();

    AdApplication::query()->create([
        'student_profile_id' => $studentProfile->id,
        'github_link' => 'https://github.com/tester',
        'expected_salary' => 1000,
        'resume' => 'resume.pdf',
        'ad_id' => $ad->id,
    ]);

    $response = $this->getJson('/api/ads');

    $response
        ->assertOk()
        ->assertJsonPath('data.0.job_name', 'Laravel Developer')
        ->assertJsonPath('data.0.applicants_count', 1)
        ->assertJsonPath('data.0.company.name', 'Acme Labs')
        ->assertJsonPath('data.0.company.description', 'Product engineering company.')
        ->assertJsonPath('data.0.company.image', 'companies/acme.png')
        ->assertJsonPath('data.0.since', '2 days ago');
});

it('allows a company to create and update its own ad', function (): void {
    $company = User::factory()->company()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($company);

    $payload = [
        'job_name' => 'Backend Engineer',
        'req1' => 'Laravel',
        'task1' => 'Ship APIs',
        'feature1' => 'Health insurance',
        'github_required' => true,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => true,
    ];

    $createResponse = $this
        ->withToken($token)
        ->postJson('/api/ads', $payload);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('data.company_id', $company->id)
        ->assertJsonPath('data.company.id', $company->id)
        ->assertJsonPath('data.applicants_count', 0);

    $adId = $createResponse->json('data.id');

    $updateResponse = $this
        ->withToken($token)
        ->patchJson('/api/ads/'.$adId, [
            'job_name' => 'Senior Backend Engineer',
        ]);

    $updateResponse
        ->assertOk()
        ->assertJsonPath('data.job_name', 'Senior Backend Engineer');

    $this->assertDatabaseHas('ads', [
        'id' => $adId,
        'job_name' => 'Senior Backend Engineer',
        'company_id' => $company->id,
    ]);
});

it('prevents a company from deleting another companys ad', function (): void {
    $owner = User::factory()->company()->create();
    $intruder = User::factory()->company()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($intruder);

    $ad = Ad::query()->create([
        'job_name' => 'Mobile Developer',
        'req1' => 'Flutter',
        'task1' => 'Build apps',
        'feature1' => 'Flexible hours',
        'github_required' => false,
        'resume_required' => true,
        'prev_work_required' => true,
        'expected_salary_required' => false,
        'company_id' => $owner->id,
    ]);

    $this
        ->withToken($token)
        ->deleteJson('/api/ads/'.$ad->id)
        ->assertForbidden();

    $this->assertDatabaseHas('ads', [
        'id' => $ad->id,
        'company_id' => $owner->id,
    ]);
});