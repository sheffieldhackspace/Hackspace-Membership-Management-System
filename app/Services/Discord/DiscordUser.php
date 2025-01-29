<?php

namespace App\Services\Discord;

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
}
