<?php

namespace App\Policies;

use App\Models\EmailAddress;
use App\Models\PostalAddress;
use App\Models\User;

class EmailAddressPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmailAddress $emailAddress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmailAddress $emailAddress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmailAddress $emailAddress): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EmailAddress $emailAddress): bool
    {
        return false;
    }

    /**
     * Legally we have to keep members contact details for 10 years after they leave.
     * This is because if the CIC folds up all members owe £1 each to pay off any debts.
     */
    public function forceDelete(User $user, EmailAddress $emailAddress): bool
    {
        return false;
    }
}
