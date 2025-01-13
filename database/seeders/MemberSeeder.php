<?php

namespace Database\Seeders;

use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\PostalAddress;
use Database\Factories\EmailAddressFactory;
use Database\Factories\MembershipHistoryFactory;
use Database\Factories\PostalAddressFactory;
use Illuminate\Database\Seeder;


class MemberSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(config('APP_ENV') === 'production') {
            return;
        }

        for($i = 0; $i < 50; $i++) {
            Member::factory()
                ->has(MembershipHistory::factory())
                ->has(PostalAddress::factory())
                ->has(EmailAddress::factory()->primary())
                ->has(EmailAddress::factory()->count(rand(0, 2)))
                ->create();
        }
    }
}
