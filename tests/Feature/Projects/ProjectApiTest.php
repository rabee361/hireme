<?php

use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret');
});

it('lists projects with applicant count customer details and since field', function (): void {
    $customer = User::factory()->customer()->create([
        'username' => 'Bright Studio',
        'description' => 'Startup building internal tools.',
        'avatar' => 'customers/bright.png',
    ]);

    $student = User::factory()->student()->create();

    $studentProfile = StudentProfile::query()->create([
        'user_id' => $student->id,
        'address' => 'Cairo',
        'hour_cost' => 50,
        'experience_years' => 2,
        'college' => 'Ain Shams',
        'title' => 'Full Stack Developer',
    ]);

    $project = Project::query()->create([
        'name' => 'Internal Dashboard',
        'details' => 'Build a dashboard for operations.',
        'tool1' => 'Laravel',
        'tool2' => 'Vue',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/dashboard.png',
        'customer_id' => $customer->id,
    ]);

    $project->forceFill([
        'created_at' => now()->subHours(5),
        'updated_at' => now()->subHours(5),
    ])->save();

    ProjectApplication::query()->create([
        'student_profile_id' => $studentProfile->id,
        'github_link' => 'https://github.com/tester',
        'expected_salary' => 1000,
        'resume' => 'resume.pdf',
        'project_id' => $project->id,
    ]);

    $response = $this->getJson('/api/projects');

    $response
        ->assertOk()
        ->assertJsonPath('data.0.name', 'Internal Dashboard')
        ->assertJsonPath('data.0.applicants_count', 1)
        ->assertJsonPath('data.0.customer.name', 'Bright Studio')
        ->assertJsonPath('data.0.customer.description', 'Startup building internal tools.')
        ->assertJsonPath('data.0.customer.image', 'customers/bright.png')
        ->assertJsonPath('data.0.since', '5 hours ago');
});

it('allows a customer to create and update its own project', function (): void {
    $customer = User::factory()->customer()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($customer);

    $payload = [
        'name' => 'Analytics Portal',
        'details' => 'Reporting portal for customers.',
        'tool1' => 'Laravel',
        'tool2' => 'React',
        'tool3' => 'PostgreSQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/analytics.png',
    ];

    $createResponse = $this
        ->withToken($token)
        ->postJson('/api/projects', $payload);

    $createResponse
        ->assertCreated()
        ->assertJsonPath('data.customer_id', $customer->id)
        ->assertJsonPath('data.customer.id', $customer->id)
        ->assertJsonPath('data.applicants_count', 0);

    $projectId = $createResponse->json('data.id');

    $updateResponse = $this
        ->withToken($token)
        ->patchJson('/api/projects/'.$projectId, [
            'name' => 'Advanced Analytics Portal',
        ]);

    $updateResponse
        ->assertOk()
        ->assertJsonPath('data.name', 'Advanced Analytics Portal');

    $this->assertDatabaseHas('projects', [
        'id' => $projectId,
        'name' => 'Advanced Analytics Portal',
        'customer_id' => $customer->id,
    ]);
});

it('prevents a customer from deleting another customers project', function (): void {
    $owner = User::factory()->customer()->create();
    $intruder = User::factory()->customer()->create();
    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($intruder);

    $project = Project::query()->create([
        'name' => 'Mobile App',
        'details' => 'Cross platform app.',
        'tool1' => 'Flutter',
        'tool2' => 'Firebase',
        'tool3' => 'REST',
        'tool4' => 'Figma',
        'tool5' => 'Jira',
        'cover_image' => 'projects/mobile.png',
        'customer_id' => $owner->id,
    ]);

    $this
        ->withToken($token)
        ->deleteJson('/api/projects/'.$project->id)
        ->assertForbidden();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'customer_id' => $owner->id,
    ]);
});