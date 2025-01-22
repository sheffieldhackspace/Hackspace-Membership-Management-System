<?php

namespace App\Listeners;

use App\Enums\RolesEnum;
use App\Events\UserCreatedEvent;

class UserCreatedListener
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
    public function handle(UserCreatedEvent $event): void
    {
        $user = $event->user;
        $user->assignRole(RolesEnum::USER->value);
    }
}
