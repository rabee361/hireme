<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@hireme.test'],
            [
                'username' => 'مدير النظام',
                'type' => UserType::Admin->value,
                'is_verified' => true,
                'email_verified_at' => now(),
                'token_version' => 0,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ],
        );
    }
}
