<?php

namespace App\Policies;

use App\Models\PostalAddress;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostalAddressPolicy
{
    /**
     * Legally we have to keep members contact details for 10 years after they leave.
     * This is because if the CIC folds up all members owe £1 each to pay off any debts.
     */
    public function forceDelete(User $user, PostalAddress $postalAddress): bool
    {
        return false;
    }
}
