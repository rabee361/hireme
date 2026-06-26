<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = \App\Models\User::where('type', \App\Enums\UserType::Customer->value)->get();

        $projectsData = [
            [
                'name' => 'E-Commerce Website',
                'details' => 'A full-featured online store with payment gateway integration.',
                'tool1' => 'Laravel',
                'tool2' => 'Vue.js',
                'tool3' => 'MySQL',
                'tool4' => 'Stripe',
                'tool5' => 'TailwindCSS',
            ],
            [
                'name' => 'Mobile App for Delivery',
                'details' => 'A cross-platform mobile application for local food delivery.',
                'tool1' => 'Flutter',
                'tool2' => 'Firebase',
                'tool3' => 'Google Maps API',
                'tool4' => 'Node.js',
                'tool5' => 'MongoDB',
            ],
            [
                'name' => 'Company Dashboard',
                'details' => 'An internal dashboard for managing employees and tasks.',
                'tool1' => 'React',
                'tool2' => 'Redux',
                'tool3' => 'Material UI',
                'tool4' => 'Express',
                'tool5' => 'PostgreSQL',
            ],
            [
                'name' => 'Portfolio Generator',
                'details' => 'A web app that allows users to create stunning portfolios easily.',
                'tool1' => 'Next.js',
                'tool2' => 'TypeScript',
                'tool3' => 'Prisma',
                'tool4' => 'Vercel',
                'tool5' => 'Framer Motion',
            ],
            [
                'name' => 'Real Estate Platform',
                'details' => 'A platform for buying and renting properties with advanced search.',
                'tool1' => 'PHP',
                'tool2' => 'Livewire',
                'tool3' => 'Alpine.js',
                'tool4' => 'Algolia',
                'tool5' => 'Bootstrap',
            ],
        ];

        foreach ($projectsData as $index => $data) {
            \App\Models\Project::create([
                'name' => $data['name'],
                'details' => $data['details'],
                'tool1' => $data['tool1'],
                'tool2' => $data['tool2'],
                'tool3' => $data['tool3'],
                'tool4' => $data['tool4'],
                'tool5' => $data['tool5'],
                'cover_image' => 'default_project_cover.jpg',
                'customer_id' => $customers[$index]->id ?? $customers->first()->id,
            ]);
        }
    }
}
