<?php

namespace App\Listeners;

use App\Enums\MembershipType;
use App\Events\MemberCreatedEvent;

class MemberCreatedListener
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
    public function handle(MemberCreatedEvent $event): void
    {
        $member = $event->member;
        $member->membershipHistory()->create([
            'membership_type' => MembershipType::UNPAIDMEMBER->value,
            'created_at' => $member->created_at,
            'updated_at' => $member->created_at
        ]);
    }
}
