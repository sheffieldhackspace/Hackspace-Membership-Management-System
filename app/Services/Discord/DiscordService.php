<?php

namespace App\Services\Discord;

use App\DiscordData\GuildMemberData;
use App\Exceptions\DiscordAPIException;
use App\Models\DiscordUser;
use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class DiscordService
{
    public function getUserModelForDiscordUser(SocialiteDiscordUser $socialiteDiscordUser): User
    {
        $discordUser = DiscordUser::updateOrCreate([
            'discord_id' => $socialiteDiscordUser->id,
        ], [
            'discord_id' => $socialiteDiscordUser->id,
            'username' => $socialiteDiscordUser->name,
            'nickname' => $socialiteDiscordUser->nickname,
            'verified' => $socialiteDiscordUser->verified,
            'avatar_hash' => $socialiteDiscordUser->avatar_hash,
        ]);

        /** @var User $user */
        $user = $discordUser->user()->updateOrCreate([], [
            'email' => $socialiteDiscordUser->email,
        ]);

        if ($user->email_verified_at === null && $socialiteDiscordUser->verified) {
            $user->email_verified_at = now();
            $user->save();
        }

        $user->discordUser()->save($discordUser);

        if ($socialiteDiscordUser->verified && ! $user->discordUser->member) {
            $member = Member::whereHas('emailAddresses',
                fn (EmailAddress|Builder $emailAddressQuery) => $emailAddressQuery->where('email_address', $socialiteDiscordUser->email))
                ->first();
            if ($member) {
                $member->emailAddresses()
                    ->where('email_address', $socialiteDiscordUser->email)
                    ->where('verified_at', null)
                    ->update([
                        'verified_at' => now(),
                    ]);
                $user->discordUser->member()->associate($member);
                $user->discordUser->save();
            }
        }

        if ($user->discordUser->member && $user->members->doesntContain($user->discordUser->member)) {
            $user->members()->save($user->discordUser->member);
        }

        return $user;
    }

    /**
     * Returns the User-Agent of the HTTP client.
     */
    private function getUserAgent(): string
    {
        return 'Sheffield Hackspace Membership Management System (https://github.com/sheffieldhackspace/Hackspace-Membership-Management-System)';
    }

    private function getHeaders()
    {
        return [
            'Authorization' => 'Bot '.config('services.discord.bot_token'),
            'User-Agent' => $this->getUserAgent(),
        ];
    }

    /**
     * @return LazyCollection<GuildMemberData>
     *
     * @throws DiscordAPIException
     */
    public function getMembersOfGuild(): LazyCollection
    {
        return LazyCollection::make(function () {
            $limit = 100;
            $last = null;
            do {
                try {
                    $members = Http::acceptJson()
                        ->withHeaders($this->getHeaders())
                        ->get(config('services.discord.api_url').'/guilds/'.config('services.discord.guild_id').'/members', [
                            'limit' => $limit,
                            'after' => $last,
                        ])
                        ->throw()
                        ->json();

                    Log::info('[DiscordService] Retrieved '.count($members).' members from Discord');
                    Log::debug('[DiscordService] Members: '.json_encode($members));
                } catch (RequestException|ConnectionException $e) {
                    throw new DiscordAPIException('Error retrieving members from Discord', 500, $e);
                }
                $count = count($members);
                if ($count > 0) {
                    $last = end($members)['user']['id'];
                    foreach ($members as $member) {
                        yield GuildMemberData::fromGuildMemberArray($member);
                    }
                }
            } while ($count === $limit);

        });

    }

    public function updateOrCreateUserFromGuildMember(GuildMemberData $guildMemberData): ?DiscordUser
    {
        $discordUser = DiscordUser::updateOrCreate([
            'discord_id' => $guildMemberData->discord_id,
        ], [
            'discord_id' => $guildMemberData->discord_id,
            'username' => $guildMemberData->username,
            'nickname' => $guildMemberData->nickname,
            'avatar_hash' => $guildMemberData->avatar_hash,
        ]);

        if ($discordUser->wasRecentlyCreated) {
            Log::info('[DiscordService] Created new DiscordUser: '.$discordUser->discord_id);
        } else {
            Log::info('[DiscordService] Updated DiscordUser: '.$discordUser->discord_id);
        }
        Log::debug('[DiscordService] DiscordUser: '.$discordUser->toJson());

        return $discordUser;

    }
}
