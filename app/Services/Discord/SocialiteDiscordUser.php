<?php

namespace App\Services\Discord;

use App\Exceptions\DiscordAuthenticationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
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

    public ?string $avatar_hash;

    /**
     * @var string[]
     */
    public array $roles;

    public function isUserInGuild(string $guild): bool
    {
        return array_any($this->guilds,
            fn ($userGuild) => $userGuild['id'] === $guild);
    }

    public function saveToSession(): void
    {
        session(['discord_user_token' => $this->token]);
    }

    /**
     * @throws DiscordAuthenticationException
     */
    public function updateUserWithGuildInfo($guildId): void
    {
        $guildMemberUrl = "https://discord.com/api/users/@me/guilds/{$guildId}/member";

        try {
            $member = Http::acceptJson()
                ->withToken($this->token)
                ->get($guildMemberUrl)
                ->throw()
                ->json();
        } catch (RequestException|ConnectionException $e) {
            throw DiscordAuthenticationException::errorRetrievingUserData($e);
        }

        $this->nickname = $member['nick'] ?? $this->nickname;
        $this->avatar_hash = $member['avatar'] ?? $this->avatar_hash;
        $this->avatar = $member['avatar'] ? sprintf('https://cdn.discordapp.com/avatars/%s/%s.png', $this->id, $member['avatar']) : $this->avatar;
        $this->roles = $member['roles'] ?? [];
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
