<?php

namespace App\Listeners;

use App\Enums\MembershipType;
use App\Enums\RolesEnum;
use App\Events\MembershipHistoryChangedEvent;

class MembershipHistoryListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MembershipHistoryChangedEvent $membershipHistoryChanged): void
    {
        $member = $membershipHistoryChanged->membershipHistory->member;
        $newMembershipType = $member->latestMembershipHistory->membership_type;
        switch ($newMembershipType) {
            case MembershipType::KEYHOLDER:
                $member->assignRole(RolesEnum::KEYHOLDER->value);
                $member->assignRole(RolesEnum::MEMBER->value);
                break;
            case MembershipType::MEMBER:
                $member->removeRole(RolesEnum::KEYHOLDER->value);
                $member->assignRole(RolesEnum::MEMBER->value);
                break;
            default:
                $member->removeRole(RolesEnum::KEYHOLDER->value);
                $member->removeRole(RolesEnum::MEMBER->value);
        }

    }
}
