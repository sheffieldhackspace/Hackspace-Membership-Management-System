<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MemberPolicy
{
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
