<?php

namespace App\Services\Discord;

use App\Models\DiscordUser;
use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

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
}
