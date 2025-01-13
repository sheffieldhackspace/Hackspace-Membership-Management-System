<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $memberRole = Role::create(['name' => RolesEnum::MEMBER->value, 'guard_name' => 'member']);
        $memberRole->givePermissionTo();

        $keyholderRole = Role::create(['name' => RolesEnum::KEYHOLDER->value, 'guard_name' => 'member']);
        $keyholderRole->givePermissionTo();

        $toolTrainerRole = Role::create(['name' => RolesEnum::TOOLTRAINER->value, 'guard_name' => 'member']);
        $toolTrainerRole->givePermissionTo();

        $pwUser = Role::create(['name' => RolesEnum::PWUSER->value, 'guard_name' => 'web']);
        $pwUser->givePermissionTo();

        $adminRole = Role::create(['name' => RolesEnum::ADMIN->value, 'guard_name' => 'member']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
