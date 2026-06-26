<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            ['username' => 'Ahmad_Student', 'email' => 'ahmad_student@example.com', 'title' => 'Backend Developer'],
            ['username' => 'Sara_Student', 'email' => 'sara_student@example.com', 'title' => 'Frontend Developer'],
            ['username' => 'Ali_Student', 'email' => 'ali_student@example.com', 'title' => 'Full Stack Developer'],
            ['username' => 'Nour_Student', 'email' => 'nour_student@example.com', 'title' => 'Mobile Developer'],
            ['username' => 'Omar_Student', 'email' => 'omar_student@example.com', 'title' => 'UI/UX Designer'],
        ];

        foreach ($students as $index => $data) {
            $user = \App\Models\User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'type' => \App\Enums\UserType::Student->value,
                'is_verified' => true,
                'email_verified_at' => now(),
                'token_version' => 0,
            ]);

            \App\Models\StudentProfile::create([
                'user_id' => $user->id,
                'address' => 'Damascus, Syria',
                'hour_cost' => 15.00 + ($index * 5),
                'experience_years' => 1 + $index,
                'college' => 'Information Technology',
                'title' => $data['title'],
            ]);
        }

        $customers = [
            ['username' => 'Khalid_Customer', 'email' => 'khalid_customer@example.com', 'title' => 'Project Manager'],
            ['username' => 'Laila_Customer', 'email' => 'laila_customer@example.com', 'title' => 'Product Owner'],
            ['username' => 'Sami_Customer', 'email' => 'sami_customer@example.com', 'title' => 'Business Analyst'],
            ['username' => 'Rana_Customer', 'email' => 'rana_customer@example.com', 'title' => 'Entrepreneur'],
            ['username' => 'Hassan_Customer', 'email' => 'hassan_customer@example.com', 'title' => 'Marketing Manager'],
        ];

        foreach ($customers as $index => $data) {
            $user = \App\Models\User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'type' => \App\Enums\UserType::Customer->value,
                'is_verified' => true,
                'email_verified_at' => now(),
                'token_version' => 0,
            ]);

            \App\Models\CustomerProfile::create([
                'user_id' => $user->id,
                'address' => 'Aleppo, Syria',
                'hour_cost' => 20.00 + ($index * 5),
                'experience_years' => 3 + $index,
                'college' => 'Business Administration',
                'title' => $data['title'],
            ]);
        }

        $companies = [
            ['username' => 'TechMinds_Co', 'email' => 'contact@techminds.com'],
            ['username' => 'Innovate_Solutions', 'email' => 'info@innovatesolutions.com'],
            ['username' => 'Future_Soft', 'email' => 'hello@futuresoft.com'],
            ['username' => 'Pioneer_Devs', 'email' => 'support@pioneerdevs.com'],
            ['username' => 'Smart_Systems', 'email' => 'admin@smartsystems.com'],
        ];

        foreach ($companies as $index => $data) {
            $user = \App\Models\User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'type' => \App\Enums\UserType::Company->value,
                'is_verified' => true,
                'email_verified_at' => now(),
                'token_version' => 0,
            ]);

            \App\Models\CompanyProfile::create([
                'user_id' => $user->id,
                'started_at' => now()->subYears(5 - $index)->format('Y-m-d'),
                'employees_count' => 10 + ($index * 10),
            ]);
        }
    }
}
