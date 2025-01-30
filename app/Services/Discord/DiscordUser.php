<?php

namespace App\Services\Discord;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

class DiscordUser extends User
{
    public bool $verified;

    /**
     * @var array{
     *     id: string,
     *     name: string,
     *     icon: string,
     *     banner: ?string,
     *     owner: bool,
     *     permissions: int,
     *     permissions_new: string,
     *     features: string[],
     * } $guilds
     */
    public array $guilds;

    public function getGuilds(): array
    {
        return $this->guilds;
    }

    public function getIsVerified(): bool
    {
        return $this->verified;
    }

    public function isUserInGuild(string $guild): bool
    {
        return array_any($this->guilds, fn($userGuild) => $userGuild['id'] === $guild);
    }

    public function saveUserInSession(): void
    {
        session(['discord_user_token' => $this->token]);
    }

    public static function getUserFromSession(): ?DiscordUser
    {
        $token = session('discord_user_token');
        if ($token === null) {
            return null;
        }

        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord-with-guilds');

        return $driver->getUserByToken($token);
    }
}
