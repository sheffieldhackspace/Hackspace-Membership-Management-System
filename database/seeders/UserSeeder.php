<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(config('APP_ENV') === 'production') {
            return;
        }

        User::query()->updateOrCreate(
            [
                'email' => 'test@test.com'
            ],
            [
                'email' => 'test@test.com',
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60),
            ]
        );
    }
}
