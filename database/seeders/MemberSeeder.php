<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MembershipHistory;
use Database\Factories\MembershipHistoryFactory;
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

        Member::factory(50)->has(MembershipHistory::factory())->create();
    }
}
