<?php

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => PermissionEnum::VIEWOWNMEMBER, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::EDITOWNMEMBER, 'guard_name' => 'member']);

        $memberRole = Role::create(['name' => RolesEnum::MEMBER->value, 'guard_name' => 'member']);
        $memberRole->givePermissionTo([PermissionEnum::VIEWOWNMEMBER, PermissionEnum::EDITOWNMEMBER]);

        $keyholderRole = Role::create(['name' => RolesEnum::KEYHOLDER->value, 'guard_name' => 'member']);
        $keyholderRole->givePermissionTo();

        $toolTrainerRole = Role::create(['name' => RolesEnum::TOOLTRAINER->value, 'guard_name' => 'member']);
        $toolTrainerRole->givePermissionTo();

        Permission::create(['name' => PermissionEnum::VIEWPWMEMBERREPORT, 'guard_name' => 'web']);
        $pwUser = Role::create(['name' => RolesEnum::PWUSER->value, 'guard_name' => 'web']);
        $pwUser->givePermissionTo([PermissionEnum::VIEWPWMEMBERREPORT]);


        Permission::create(['name' => PermissionEnum::VIEWUSERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::EDITUSERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::VIEWMEMBERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::EDITMEMBERS, 'guard_name' => 'member']);
        Permission::create(['name' => PermissionEnum::CREATEMEMBER, 'guard_name' => 'member']);
        $adminRole = Role::create(['name' => RolesEnum::ADMIN->value, 'guard_name' => 'member']);
        $adminRole->givePermissionTo(Permission::all()->where('guard_name', 'member')->pluck('name')->toArray());
    }
}
