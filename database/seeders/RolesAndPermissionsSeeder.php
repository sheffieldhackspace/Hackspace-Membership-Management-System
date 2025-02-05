<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permission and roles for users
        Permission::create(['name' => PermissionEnum::EDITOWNUSER, 'guard_name' => 'web']);
        Permission::create(['name' => PermissionEnum::VIEWOWNUSER, 'guard_name' => 'web']);

        $userRole = Role::create(['name' => RolesEnum::USER->value, 'guard_name' => 'web']);
        $userRole->givePermissionTo([PermissionEnum::EDITOWNUSER, PermissionEnum::VIEWOWNUSER]);

        Permission::create(['name' => PermissionEnum::VIEWPWMEMBERREPORT, 'guard_name' => 'web']);
        $pwUser = Role::create(['name' => RolesEnum::PWUSER->value, 'guard_name' => 'web']);
        $pwUser->givePermissionTo([PermissionEnum::VIEWPWMEMBERREPORT]);

        // Permission and roles for members
        Permission::create(['name' => PermissionEnum::VIEWOWNMEMBER, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::EDITOWNMEMBER, 'guard_name' => 'member']);

        $memberRole = Role::create(['name' => RolesEnum::MEMBER->value, 'guard_name' => 'member']);
        $memberRole->givePermissionTo([PermissionEnum::VIEWOWNMEMBER, PermissionEnum::EDITOWNMEMBER]);

        $keyholderRole = Role::create(['name' => RolesEnum::KEYHOLDER->value, 'guard_name' => 'member']);
        $keyholderRole->givePermissionTo();

        $toolTrainerRole = Role::create(['name' => RolesEnum::TOOLTRAINER->value, 'guard_name' => 'member']);
        $toolTrainerRole->givePermissionTo();

        Permission::create(['name' => PermissionEnum::VIEWUSERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::EDITUSERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::VIEWMEMBERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::EDITMEMBERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::CREATEMEMBER, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::CHANGEMEMBERSHIPTYPE, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::ADMINISTERDISCORD, 'guard_name' => 'member']);
        $adminRole = Role::create(['name' => RolesEnum::ADMIN->value, 'guard_name' => 'member']);
        $adminRole->givePermissionTo(Permission::all()->where('guard_name', 'member')->pluck('name')->toArray());
    }
}
