<?php

namespace Tests;

use App\Enums\MembershipType;
use App\Enums\RolesEnum;
use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\PostalAddress;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function asAdminUser(): User
    {
        $user = User::factory([])
            ->has(Member::factory([
                'name' => 'Admin User',
                'known_as' => 'Admin',
            ])
                ->has(MembershipHistory::factory([
                    'membership_type' => MembershipType::KEYHOLDER,
                ]))
                ->isTrustee()
            )->create();
        $this->actingAs($user);

        return $user;
    }

    public function asPWUser(): User
    {
        $user = User::factory()
            ->create();
        $this->actingAs($user);

        $user->assignRole(RolesEnum::PWUSER);

        return $user;
    }
}
