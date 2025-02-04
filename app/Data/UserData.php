<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $emailAddress,
        public ?string $emailVerifiedAt,
        public array|Lazy $members,
        public DiscordUserData|Lazy $discordUser,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            emailAddress: $user->email ?? '',
            emailVerifiedAt: $user->email_verified_at ? $user->email_verified_at->toDateString() : null,
            members: Lazy::whenLoaded(
                'members',
                $user,
                fn () => $user->members->map(fn ($member) => MemberData::fromModel($member))
            ),
            discordUser: Lazy::whenLoaded(
                'discordUser',
                $user,
                fn () => DiscordUserData::fromModel($user->discordUser)
            ),
        );
    }
}
