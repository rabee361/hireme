<?php

use App\Enums\UserType;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Student;
use App\Models\User;

test('typed user models are scoped to their type', function () {
    User::factory()->student()->create();
    User::factory()->customer()->create();
    User::factory()->company()->create();
    User::factory()->admin()->create();

    expect(Student::query()->count())->toBe(1)
        ->and(Customer::query()->count())->toBe(1)
        ->and(Company::query()->count())->toBe(1);
});

test('typed user models assign their type when created directly', function () {
    $student = Student::create([
        'username' => 'student-one',
        'email' => 'student@example.com',
        'password' => 'password',
    ]);

    expect($student->type)->toBe(UserType::Student);
});