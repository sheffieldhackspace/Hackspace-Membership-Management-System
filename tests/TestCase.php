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
        $user = User::factory()->isAdmin()->create();
        $this->actingAs($user);

        return $user;
    }
}
