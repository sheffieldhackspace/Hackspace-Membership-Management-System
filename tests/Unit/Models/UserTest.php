<?php

namespace Tests\Unit\Models;

use App\Enums\PermissionEnum;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpDocumentor\Reflection\Types\Boolean;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(\App\Models\Member::class)]
class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_permissions_gets_permissions_from_user(): void
    {
        $user = User::factory()->isPWUser()->create();

        $permissions = $user->getAllPermissions();

        $this->assertContains(PermissionEnum::VIEWPWMEMBERREPORT, $permissions);
        $this->assertContainsOnlyString($permissions);
    }

    public function test_get_all_permissions_gets_unique_permissions_from_members(): void
    {
        $user = User::factory()
            ->has(Member::factory()->isMember()->count(2))
            ->has(Member::factory()->isKeyholder())
            ->create();

        $permissions = $user->getAllPermissions();
        $this->assertContainsOnlyUniqueValues($permissions);
        $this->assertContainsOnlyString($permissions);
    }

    /**
     * @param callable(): User $user
     * @param mixed $input
     * @param bool $expectedResponse
     * @return void
     */
    #[DataProvider('provideCheckPermission')]
    public function test_check_permissions(callable $user, mixed $input, bool $expectedResponse): void
    {
        $this->assertEquals($expectedResponse, $user()->checkPermissions($input));
    }


    static public function provideCheckPermission(): \Generator
    {
        yield [
            fn() => User::factory()->isPWUser()->create(),
            PermissionEnum::VIEWPWMEMBERREPORT,
            true
        ];

        yield [
            fn() => User::factory()->isPWUser()->create(),
            collect([PermissionEnum::VIEWPWMEMBERREPORT]),
            true
        ];

        yield [
            fn() => User::factory()->isPWUser()->create(),
            [PermissionEnum::VIEWPWMEMBERREPORT],
            true
        ];

        yield [
            fn() => User::factory()->isPWUser()->create(),
            collect([PermissionEnum::VIEWPWMEMBERREPORT->value, PermissionEnum::EDITMEMBERS]),
            true
        ];

        yield [
            fn() => User::factory()->isPWUser()->create(),
            [PermissionEnum::VIEWPWMEMBERREPORT->value, PermissionEnum::EDITMEMBERS],
            true
        ];

        yield [
            fn() => User::factory()->has(Member::factory()->isTrustee())->create(),
            PermissionEnum::EDITMEMBERS,
            true
        ];

        yield [
            fn() => User::factory()->has(Member::factory()->isMember())->create(),
            PermissionEnum::EDITMEMBERS,
            false
        ];

        yield [
            fn() => User::factory()->create(),
            [PermissionEnum::VIEWPWMEMBERREPORT],
            false
        ];

        yield [
            fn() => User::factory()->has(Member::factory()->isMember())->create(),
            PermissionEnum::EDITOWNMEMBER,
            true
        ];

        yield [
            fn() => User::factory()->create(),
            PermissionEnum::EDITOWNMEMBER,
            false
        ];
    }
}
