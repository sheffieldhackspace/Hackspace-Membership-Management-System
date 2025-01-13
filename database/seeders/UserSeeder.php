<?php

namespace Database\Seeders;

use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\PostalAddress;
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

        User::factory()
            ->has(Member::factory()
                ->has(MembershipHistory::factory())
                ->has(PostalAddress::factory())
                ->has(EmailAddress::factory()->primary())
                ->has(EmailAddress::factory()->count(rand(0, 2)))
                ->isTrustee()
            )
            ->create([
                'email' => 'test@test.com',
                'password' => Hash::make('password'),
                'remember_token' => Str::random(60)
            ]);
    }
}
