<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('completes the full project lifecycle from creation to application review', function (): void {
    // ── Act 1: Customer authenticates and creates a Project ──
    $customer = User::factory()->customer()->create([
        'username' => 'ClientCorp',
        'description' => 'A client with projects.',
    ]);

    $customerToken = auth('api')->claims(['token_type' => 'access'])->fromUser($customer);

    $createProjectResponse = $this->withToken($customerToken)->postJson('/api/projects', [
        'name' => 'E-commerce Platform',
        'details' => 'Build a full e-commerce platform.',
        'tool1' => 'Laravel',
        'tool2' => 'Vue',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/ecommerce.png',
    ]);

    $createProjectResponse
        ->assertCreated()
        ->assertJsonPath('data.name', 'E-commerce Platform')
        ->assertJsonPath('data.customer_id', $customer->id);

    $projectId = $createProjectResponse->json('data.id');

    // ── Act 2: Student authenticates and applies to the Project ──
    $student = User::factory()->student()->create([
        'username' => 'FullStackStudent',
    ]);
    $student->studentProfile()->create([
        'title' => 'Full Stack Developer',
        'address' => 'Alexandria',
        'hour_cost' => 75,
        'experience_years' => 5,
    ]);

    $studentToken = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $applyResponse = $this->withToken($studentToken)->postJson('/api/project-applications', [
        'project_id' => $projectId,
        'github_link' => 'https://github.com/fullstackstudent',
        'expected_salary' => 3000,
        'resume' => 'https://resumes.com/fullstackstudent.pdf',
    ]);

    $applyResponse
        ->assertCreated()
        ->assertJsonPath('message', 'Project application submitted successfully.');

    $applicationId = $applyResponse->json('data.id');

    // ── Act 3: Customer views applications on their project ──
    $applicationsResponse = $this->withToken($customerToken)
        ->getJson('/api/projects/'.$projectId.'/applications');

    $applicationsResponse
        ->assertOk()
        ->assertJsonPath('message', 'Project applications retrieved successfully.')
        ->assertJsonCount(1, 'data');

    // ── Assert: Ensure the student's application is visible and structured correctly ──
    $applicationData = $applicationsResponse->json('data.0');

    expect($applicationData['id'])->toBe($applicationId);
    expect($applicationData['student']['username'])->toBe('FullStackStudent');
    expect($applicationData['github_link'])->toBe('https://github.com/fullstackstudent');
    expect($applicationData['expected_salary'])->toBe(3000);
    expect($applicationData['resume'])->toBe('https://resumes.com/fullstackstudent.pdf');

    // Verify timestamps
    expect($applicationData['created_at'])->not->toBeNull();
    expect($applicationData['updated_at'])->not->toBeNull();

    // Verify nested project data
    expect($applicationData['project']['name'])->toBe('E-commerce Platform');
    expect($applicationData['project']['customer']['name'])->toBe('ClientCorp');
});
