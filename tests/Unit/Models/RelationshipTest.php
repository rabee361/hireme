<?php

use App\Models\Ad;
use App\Models\AdApplication;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Project;
use App\Models\ProjectApplication;
use App\Models\Student;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Helper to create a user without relying on facades in unit tests.
 */
function createUser(string $type): User
{
    return User::query()->create([
        'type' => $type,
        'username' => fake()->unique()->userName(),
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('password'),
        'is_verified' => true,
        'email_verified_at' => now(),
        'token_version' => 0,
    ]);
}

test('ad has many ad applications', function (): void {
    $company = createUser('company');
    $student = createUser('student');

    $studentProfile = StudentProfile::query()->create(['user_id' => $student->id]);

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

    $application = AdApplication::query()->create([
        'student_profile_id' => $studentProfile->id,
        'github_link' => 'https://github.com/test',
        'expected_salary' => 1000,
        'resume' => 'resume.pdf',
        'ad_id' => $ad->id,
    ]);

    expect($ad->applications)->toHaveCount(1);
    expect($ad->applications->first()->id)->toBe($application->id);
});

test('project has many project applications', function (): void {
    $customer = createUser('customer');
    $student = createUser('student');

    $studentProfile = StudentProfile::query()->create(['user_id' => $student->id]);

    $project = Project::query()->create([
        'name' => 'Internal Dashboard',
        'details' => 'Build a dashboard.',
        'tool1' => 'Laravel',
        'tool2' => 'Vue',
        'tool3' => 'MySQL',
        'tool4' => 'Redis',
        'tool5' => 'Docker',
        'cover_image' => 'projects/dashboard.png',
        'customer_id' => $customer->id,
    ]);

    $application = ProjectApplication::query()->create([
        'student_profile_id' => $studentProfile->id,
        'github_link' => 'https://github.com/test',
        'expected_salary' => 1000,
        'resume' => 'resume.pdf',
        'project_id' => $project->id,
    ]);

    expect($project->applications)->toHaveCount(1);
    expect($project->applications->first()->id)->toBe($application->id);
});

test('student resolves profile relationship', function (): void {
    $user = createUser('student');
    /** @var Student $student */
    $student = Student::findOrFail($user->id);

    $profile = $student->profile()->create([
        'title' => 'Frontend Developer',
        'address' => 'Cairo',
    ]);

    expect($student->profile->id)->toBe($profile->id);
    expect($student->profile->title)->toBe('Frontend Developer');
});

test('company resolves profile relationship', function (): void {
    $user = createUser('company');
    /** @var Company $company */
    $company = Company::findOrFail($user->id);

    $profile = $company->profile()->create([
        'employees_count' => 50,
        'started_at' => '2020-01-01',
    ]);

    expect($company->profile->id)->toBe($profile->id);
    expect((int) $company->profile->employees_count)->toBe(50);
});

test('customer resolves profile relationship', function (): void {
    $user = createUser('customer');
    /** @var Customer $customer */
    $customer = Customer::findOrFail($user->id);

    $profile = $customer->profile()->create([
        'title' => 'Project Owner',
        'address' => 'Alexandria',
    ]);

    expect($customer->profile->id)->toBe($profile->id);
    expect($customer->profile->title)->toBe('Project Owner');
});

test('company saved students pivot relationship', function (): void {
    $companyUser = createUser('company');
    $studentUser = createUser('student');

    /** @var Company $company */
    $company = Company::findOrFail($companyUser->id);
    /** @var Student $student */
    $student = Student::findOrFail($studentUser->id);

    $company->savedStudents()->attach($student->id);

    expect($company->savedStudents)->toHaveCount(1);
    expect($company->savedStudents->first()->id)->toBe($student->id);

    // Test inverse relationship
    expect($student->savedByCompanies)->toHaveCount(1);
    expect($student->savedByCompanies->first()->id)->toBe($company->id);
});

test('student saved ads pivot relationship', function (): void {
    $company = createUser('company');
    $student = createUser('student');

    $ad = Ad::query()->create([
        'job_name' => 'Backend Engineer',
        'req1' => 'PHP',
        'task1' => 'Build APIs',
        'feature1' => 'Remote',
        'github_required' => true,
        'resume_required' => true,
        'prev_work_required' => false,
        'expected_salary_required' => false,
        'company_id' => $company->id,
    ]);

    /** @var Student $studentModel */
    $studentModel = Student::findOrFail($student->id);
    $studentModel->savedAds()->attach($ad->id);

    expect($studentModel->savedAds)->toHaveCount(1);
    expect($studentModel->savedAds->first()->id)->toBe($ad->id);

    // Test inverse relationship
    $ad->refresh();
    expect($ad->savedByStudents)->toHaveCount(1);
    expect($ad->savedByStudents->first()->id)->toBe($studentModel->id);
});
