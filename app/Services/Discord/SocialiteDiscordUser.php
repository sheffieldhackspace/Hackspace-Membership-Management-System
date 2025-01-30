<?php

namespace App\Services\Discord;

use App\Models\DiscordUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class SocialiteDiscordUser extends SocialiteUser
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

    public string $avatar_hash;

    public function getGuilds(): array
    {
        return $this->guilds;
    }

    public function getIsVerified(): bool
    {
        return $this->verified;
    }

    public function getAvatarHash(): string
    {
        return $this->avatar_hash;
    }

    public function isUserInGuild(string $guild): bool
    {
        return array_any($this->guilds, fn($userGuild) => $userGuild['id'] === $guild);
    }

    public function saveToSession(): void
    {
        session(['discord_user_token' => $this->token]);
    }

    public function getUserModel(): User
    {
        $user = User::whereHas(
            'discordUser',
            fn(Builder|DiscordUser $query) => $query->whereDiscordId($this->id)
        )->first();

        if ($user) {
            return $user;
        }

        $user = User::create();
        $user->discordUser()->create([
            'discord_id' => $this->id,
            'username' => $this->name,
            'nickname' => $this->nickname,
            'verified' => $this->verified,
            'avatar_hash' => $this->avatar_hash,
        ]);

        return $user;
    }

    public static function getFromSession(): ?SocialiteDiscordUser
    {
        $token = session('discord_user_token');
        if ($token === null) {
            return null;
        }

        /** @var DiscordProvider $driver */
        $driver = Socialite::driver('discord-with-guilds');

        /** @var SocialiteDiscordUser $user */
        $user = $driver->userFromToken($token);

        return $user;
    }
}
