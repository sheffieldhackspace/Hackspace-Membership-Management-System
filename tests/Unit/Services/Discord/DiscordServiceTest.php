<?php

namespace Tests\Unit\Services\Discord;

use App\DiscordData\GuildMemberData;
use App\Exceptions\DiscordAPIException;
use App\Services\Discord\DiscordService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\WithFakeDiscordApi;

#[CoversClass(DiscordService::class)]
class DiscordServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFakeDiscordApi;
    use WithFaker;

    public function test_get_members_of_guild()
    {
        $this->fakeGuildMembersAPI(150);

        $discordService = new DiscordService;

        $members = $discordService->getMembersOfGuild()->collect();

        $this->assertEquals(150, $members->count());

        $member = $members->first();
        $this->assertInstanceOf(GuildMemberData::class, $member);
    }

    public function test_get_members_of_guild_handles_exception()
    {
        $this->fakeGuildMembersAPI(0, 500);

        $discordService = new DiscordService;

        $this->expectException(DiscordAPIException::class);

        $discordService->getMembersOfGuild()->all();
    }

    public function test_update_or_create_user_from_guild_member()
    {
        $discordService = new DiscordService;

        $guildMemberData = new GuildMemberData(
            discord_id: '123',
            username: 'testuser',
            nickname: 'testnick',
            avatar_hash: 'avatarhash',
            bot: false
        );

        $discordService->updateOrCreateUserFromGuildMember($guildMemberData);

        $this->assertDatabaseHas('discord_users', [
            'discord_id' => '123',
            'username' => 'testuser',
            'nickname' => 'testnick',
            'avatar_hash' => 'avatarhash',
        ]);
    }
}
