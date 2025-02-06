<?php

namespace App\DiscordData;

use Spatie\LaravelData\Data;

class GuildMemberData extends Data
{
    public function __construct(
        public string $discord_id,
        public string $username,
        public ?string $nickname,
        public ?string $avatar_hash,
        public bool $bot,
    ) {}

    /**
     * Accepts a single array of guild member data and returns a new instance of GuildMemberData
     *
     * This isnt every field from the array, just the ones we care about
     *
     * @param array{
     *     avatar: ?string,
     *     banner: ?string,
     *     joined_at: string,
     *     nick: ?string,
     *     pending: bool,
     *     roles: string[],
     *     user: array{
     *     id: string,
     *     username: string,
     *     avatar: ?string,
     *     banner: ?string,
     *     global_name: ?string,
     *     bot?: bool,
     *     }
     * } $guildMember
     */
    public static function fromGuildMemberArray(array $guildMember): self
    {
        return new self(
            discord_id: $guildMember['user']['id'],
            username: $guildMember['user']['username'],
            nickname: $guildMember['nick'] ?? $guildMember['user']['global_name'] ?? $guildMember['user']['username'],
            avatar_hash: $guildMember['avatar'] ?? $guildMember['user']['avatar'],
            bot: $guildMember['user']['bot'] ?? false,
        );
    }
}
