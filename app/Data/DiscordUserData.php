<?php

namespace App\Data;

use App\Models\DiscordUser;
use Spatie\LaravelData\Data;

class DiscordUserData extends Data
{
    public function __construct(
        public string $id,
        public string $discord_id,
        public string $username,
        public bool $verified,
        public ?string $avatar,
    ) {}

    public static function fromModel(DiscordUser $discordUser): self
    {
        return new self(
            id: $discordUser->id,
            discord_id: $discordUser->discord_id,
            username: $discordUser->username,
            verified: $discordUser->verified,
            avatar: null,
        );
    }
}
