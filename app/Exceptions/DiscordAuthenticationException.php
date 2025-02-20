<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

final class DiscordAuthenticationException extends HttpException
{
    public static function notInDiscordGuild(string $guildID = ''): self
    {
        return new self(401, "User is not the provided Discord guild. {$guildID}", null, []);
    }

    public static function errorRetrievingAccessToken(Throwable $error): self
    {
        return new self(401, 'Error retrieving access token from discord', $error, []);
    }

    public static function errorRetrievingRefreshToken(Throwable $error): self
    {
        return new self(401, 'Error retrieving refresh token from discord', $error, []);
    }

    public static function errorRetrievingUserData(Throwable $error): self
    {
        return new self(401, 'Error retrieving user data from discord', $error, []);
    }
}
