<?php

namespace App\Listeners;

use App\Enums\RolesEnum;
use App\Events\TrusteeHistoryChangedEvent;

class TrusteeHistoryListener
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
    public function handle(TrusteeHistoryChangedEvent $event): void
    {
        $member = $event->trusteeHistory->member;
        $trusteeHistory = $member->latestTrusteeHistory;
        if ($trusteeHistory->isTrustee()->exists()) {
            $member->assignRole(RolesEnum::ADMIN->value);
        } else {
            $member->removeRole(RolesEnum::ADMIN->value);
        }
    }
}
