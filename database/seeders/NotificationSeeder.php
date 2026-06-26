<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = \App\Models\User::where('type', \App\Enums\UserType::Student->value)->get();
        $companies = \App\Models\User::where('type', \App\Enums\UserType::Company->value)->get();

        if ($students->isEmpty() || $companies->isEmpty()) {
            return;
        }

        $notificationsData = [
            'Your application for Senior Backend Engineer has been received.',
            'You have a new message from TechMinds Co.',
            'Your profile has been viewed by Innovate Solutions.',
            'A new job matching your skills was posted.',
            'Congratulations, you have been shortlisted for an interview.'
        ];

        foreach ($notificationsData as $index => $content) {
            $student = $students[$index] ?? $students->first();
            $company = $companies[$index] ?? $companies->first();

            \App\Models\Notification::create([
                'content' => $content,
                'user_id' => $student->id,
                'user_type' => \App\Enums\UserType::Student->value,
                'sender_id' => $company->id,
            ]);
        }
    }
}
