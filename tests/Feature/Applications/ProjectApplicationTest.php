<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('jwt.secret', 'testing-secret-key-that-is-at-least-thirty-two-characters');
});

it('allows a student to apply to a project', function (): void {
    $customer = User::factory()->customer()->create();
    $student = User::factory()->student()->create();
    $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

    $project = Project::query()->create([
        'name' => 'Analytics Portal',
        'details' => 'Reporting portal.',
        'tool1' => 'Laravel',
        'tool2' => 'Vue',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/portal.png',
        'customer_id' => $customer->id,
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)->postJson('/api/project-applications', [
        'project_id' => $project->id,
        'github_link' => 'https://github.com/devstudent',
        'resume' => 'https://resumes.com/devstudent.pdf',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Project application submitted successfully.')
        ->assertJsonStructure([
            'message',
            'data' => [
                'id', 'project_id', 'student_profile_id',
                'github_link', 'resume', 'created_at', 'updated_at', 'student', 'project',
            ],
        ]);

    expect($response->json('data.github_link'))->toBe('https://github.com/devstudent');
    expect($response->json('data.resume'))->toBe('https://resumes.com/devstudent.pdf');
});

it('prevents a student from applying twice to the same project', function (): void {
    $customer = User::factory()->customer()->create();
    $student = User::factory()->student()->create();
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

    $project = Project::query()->create([
        'name' => 'Internal Tool',
        'details' => 'Internal tool project.',
        'tool1' => 'Laravel',
        'tool2' => 'React',
        'tool3' => 'PostgreSQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/tool.png',
        'customer_id' => $customer->id,
    ]);

    $profile->projectApplications()->create([
        'project_id' => $project->id,
        'github_link' => 'https://github.com/devstudent',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $this->withToken($token)
        ->postJson('/api/project-applications', [
            'project_id' => $project->id,
            'github_link' => 'https://github.com/devstudent',
            'resume' => 'resume.pdf',
        ])
        ->assertStatus(409)
        ->assertJsonPath('message', 'You have already applied to this project.');
});

it('allows a student to update their project application', function (): void {
    $customer = User::factory()->customer()->create();
    $student = User::factory()->student()->create();
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

    $project = Project::query()->create([
        'name' => 'Dashboard',
        'details' => 'Dashboard project.',
        'tool1' => 'Laravel',
        'tool2' => 'Vue',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/dashboard.png',
        'customer_id' => $customer->id,
    ]);

    $application = $profile->projectApplications()->create([
        'project_id' => $project->id,
        'github_link' => 'https://github.com/old',
        'expected_salary' => 500,
        'resume' => 'old-resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)->patchJson('/api/project-applications/'.$application->id, [
        'github_link' => 'https://github.com/updated',
        'expected_salary' => 1000,
        'resume' => 'updated-resume.pdf',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Project application updated successfully.')
        ->assertJsonPath('data.github_link', 'https://github.com/updated')
        ->assertJsonPath('data.expected_salary', 1000)
        ->assertJsonPath('data.resume', 'updated-resume.pdf');
});

it('allows a student to delete (withdraw) their project application', function (): void {
    $customer = User::factory()->customer()->create();
    $student = User::factory()->student()->create();
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

    $project = Project::query()->create([
        'name' => 'API Service',
        'details' => 'API service project.',
        'tool1' => 'Laravel',
        'tool2' => 'React',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/api.png',
        'customer_id' => $customer->id,
    ]);

    $application = $profile->projectApplications()->create([
        'project_id' => $project->id,
        'github_link' => 'https://github.com/devstudent',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $this->withToken($token)
        ->deleteJson('/api/project-applications/'.$application->id)
        ->assertOk()
        ->assertJsonPath('message', 'Project application deleted successfully.');

    $this->assertDatabaseMissing('project_applications', ['id' => $application->id]);
});

it('allows a student to view their project applications', function (): void {
    $customer = User::factory()->customer()->create([
        'username' => 'ClientCorp',
        'description' => 'A client company.',
    ]);
    $student = User::factory()->student()->create(['username' => 'DevStudent']);
    $profile = $student->studentProfile()->create([
        'title' => 'Full Stack Developer',
        'address' => 'Cairo',
    ]);

    $project = Project::query()->create([
        'name' => 'Web App',
        'details' => 'Web application.',
        'tool1' => 'Laravel',
        'tool2' => 'Vue',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/webapp.png',
        'customer_id' => $customer->id,
    ]);

    $profile->projectApplications()->create([
        'project_id' => $project->id,
        'github_link' => 'https://github.com/devstudent',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($student);

    $response = $this->withToken($token)->getJson('/api/student/my-project-applications');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'My project applications retrieved successfully.')
        ->assertJsonCount(1, 'data');

    expect($response->json('data.0.project.name'))->toBe('Web App');
    expect($response->json('data.0.student.username'))->toBe('DevStudent');
    expect($response->json('data.0.project.customer.name'))->toBe('ClientCorp');
});

it('allows a customer to view applications for their project', function (): void {
    $customer = User::factory()->customer()->create();
    $student = User::factory()->student()->create(['username' => 'ApplicantTwo']);
    $profile = $student->studentProfile()->create([
        'title' => 'Developer',
        'address' => 'Cairo',
    ]);

    $project = Project::query()->create([
        'name' => 'Enterprise Suite',
        'details' => 'Enterprise suite project.',
        'tool1' => 'Laravel',
        'tool2' => 'React',
        'tool3' => 'PostgreSQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/enterprise.png',
        'customer_id' => $customer->id,
    ]);

    $profile->projectApplications()->create([
        'project_id' => $project->id,
        'github_link' => 'https://github.com/applicant2',
        'resume' => 'resume.pdf',
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($customer);

    $response = $this->withToken($token)->getJson('/api/projects/'.$project->id.'/applications');

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Project applications retrieved successfully.')
        ->assertJsonCount(1, 'data');

    expect($response->json('data.0.student.username'))->toBe('ApplicantTwo');
    expect($response->json('data.0.github_link'))->toBe('https://github.com/applicant2');
    expect($response->json('data.0.project_id'))->toBe($project->id);
});

it('prevents a customer from viewing applications for another customers project', function (): void {
    $owner = User::factory()->customer()->create();
    $intruder = User::factory()->customer()->create();

    $project = Project::query()->create([
        'name' => 'Secret Project',
        'details' => 'Secret project.',
        'tool1' => 'Laravel',
        'tool2' => 'Vue',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/secret.png',
        'customer_id' => $owner->id,
    ]);

    $token = auth('api')->claims(['token_type' => 'access'])->fromUser($intruder);

    $this->withToken($token)
        ->getJson('/api/projects/'.$project->id.'/applications')
        ->assertForbidden();
});
