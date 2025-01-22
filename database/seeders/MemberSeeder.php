<?php

namespace Database\Seeders;

use App\Enums\MembershipType;
use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\PostalAddress;
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

        $members = collect();
        for($i = 0; $i < 50; $i++) {
            $createdAt = now()->subDays(rand(0, 365*10));
            $members->add(Member::factory([
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ])
                ->has(PostalAddress::factory())
                ->has(EmailAddress::factory()->primary())
                ->has(EmailAddress::factory()->count(rand(0, 2)))
                ->create());
        }

        $paidMembers = $members
            ->filter(function (Member $member) {
                return $member->created_at->diffInYears(now()) >= 1;
        })->filter(function (Member $member) {
            return rand(0, 1) === 1;
        })->each(function (Member $member) {
            $member->membershipHistory()->create([
                'membership_type' => MembershipType::MEMBER,
                'created_at' => $member->created_at->addDays(rand(0, 365)),
                'updated_at' => $member->created_at->addDays(rand(0, 365)),
            ]);
        });

        $keyholdersMembers = $members
            ->filter(function (Member $member) {
                return $member->latestMembershipHistory->created_at->diffInYears(now()) >= 1;
        })->filter(function (Member $member) {
            return rand(1, 4) === 1;
        })->each(function (Member $member) {
            $member->membershipHistory()->create([
                'membership_type' => MembershipType::KEYHOLDER,
                'created_at' => $member->latestMembershipHistory->created_at->addDays(rand(0, 365)),
                'updated_at' => $member->latestMembershipHistory->created_at->addDays(rand(0, 365)),
            ]);
        });

        $keyholdersMembers->pop(4)->filter(function (Member $member) {
            return $member->latestMembershipHistory->created_at->diffInYears(now()) >= 1;
        })->pop(4)
            ->each(function (Member $member) {
                $member->trusteeHistory()->create([
                    'elected_at' => $member->created_at->addDays(rand(0, 365)),
                ]);
            });
    }
}
