<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = \App\Models\User::where('type', \App\Enums\UserType::Company->value)->get();

        $adsData = [
            [
                'job_name' => 'Senior Backend Engineer',
                'req1' => 'PHP/Laravel 5+ years',
                'task1' => 'Develop scalable APIs',
                'feature1' => 'Health Insurance',
            ],
            [
                'job_name' => 'Frontend React Developer',
                'req1' => 'React/Redux 3+ years',
                'task1' => 'Build interactive UI',
                'feature1' => 'Remote Work',
            ],
            [
                'job_name' => 'UI/UX Designer',
                'req1' => 'Figma Expert',
                'task1' => 'Design user flows and wireframes',
                'feature1' => 'Flexible Hours',
            ],
            [
                'job_name' => 'Project Manager',
                'req1' => 'Agile/Scrum Experience',
                'task1' => 'Manage team sprints',
                'feature1' => 'Annual Bonus',
            ],
            [
                'job_name' => 'DevOps Engineer',
                'req1' => 'Docker/Kubernetes',
                'task1' => 'Maintain CI/CD pipelines',
                'feature1' => 'Gym Membership',
            ]
        ];

        foreach ($adsData as $index => $data) {
            \App\Models\Ad::create([
                'job_name' => $data['job_name'],
                'req1' => $data['req1'],
                'task1' => $data['task1'],
                'feature1' => $data['feature1'],
                'github_required' => $index % 2 == 0,
                'resume_required' => true,
                'prev_work_required' => $index % 2 != 0,
                'expected_salary_required' => true,
                'company_id' => $companies[$index]->id ?? $companies->first()->id,
            ]);
        }
    }
}
