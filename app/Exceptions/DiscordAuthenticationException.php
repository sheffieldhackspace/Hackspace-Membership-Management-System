<?php

namespace App\Exceptions;

use Spatie\Permission\Exceptions\UnauthorizedException as BaseUnauthorizedException;
use Throwable;

class DiscordAuthenticationException extends BaseUnauthorizedException
{
    public static function notInDiscordGuild(string $guildID = ''): self
    {
        return new static(403, "User is not the provided Discord guild. {$guildID}", null, []);
    }

    public static function errorRetrievingAccessToken(Throwable $error): self
    {
        return new static(403, 'Error retrieving access token from discord', $error, []);
    }

    public static function errorRetrievingRefreshToken(Throwable $error): self
    {
        return new static(403, 'Error retrieving refresh token from discord', $error, []);
    }

    public static function errorRetrievingUserData(Throwable $error): self
    {
        return new static(403, 'Error retrieving user data from discord', $error, []);
    }
}
