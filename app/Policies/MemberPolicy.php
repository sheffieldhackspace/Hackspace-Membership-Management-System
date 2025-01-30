<?php

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Member;
use App\Models\User;

class MemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissions([PermissionEnum::VIEWMEMBERS]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Member $member): bool
    {
        return $user->checkPermissions([PermissionEnum::VIEWMEMBERS])
            || $user->members->contains('id', '=', $member->id) && $user->checkPermissions([PermissionEnum::VIEWOWNMEMBER]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissions([PermissionEnum::CREATEMEMBER]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Member $member): bool
    {
        return $user->checkPermissions([PermissionEnum::EDITMEMBERS])
        || $user->members->contains('id', '=', $member->id) && $user->checkPermissions([PermissionEnum::EDITOWNMEMBER]);
    }

    /**
     * Determine whether the user can change the membership type of the model.
     */
    public function changeMembershipType(User $user, Member $member): bool
    {
        return $user->checkPermissions([PermissionEnum::CHANGEMEMBERSHIPTYPE]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Member $member): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Member $member): bool
    {
        return false;
    }

    /**
     * Members cannot be deleted only marked as inactive.
     * Legally we have to keep their data for 10 years after they leave.
     * This is because if the CIC folds up all members owe £1 each to pay off any debts.
     */
    public function forceDelete(User $user, Member $member): bool
    {
        return false;
    }
}
