<?php

namespace App\Exceptions;

use Spatie\Permission\Exceptions\UnauthorizedException as BaseUnauthorizedException;

class UnauthorizedException extends BaseUnauthorizedException
{
    public static function notInDiscordGuild(): self
    {
        return new static(403, 'User is not the Discord server.', null, []);
    }
}
