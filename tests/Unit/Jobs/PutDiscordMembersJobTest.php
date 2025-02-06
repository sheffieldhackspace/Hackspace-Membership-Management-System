<?php

namespace Tests\Unit\Jobs;

use App\Jobs\PutDiscordMembersJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\WithFakeDiscordApi;

#[CoversClass(PutDiscordMembersJob::class)]
class PutDiscordMembersJobTest extends TestCase
{
    use RefreshDatabase;
    use WithFakeDiscordApi;
    use WithFaker;

    public function test_creates_all_members_of_guild_except_bots()
    {
        $this->fakeGuildMembersAPI([
            [
                'user' => [
                    'id' => '123',
                    'username' => 'testuser',
                    'avatar' => 'avatarhash',
                    'bot' => false,
                ],
                'nick' => 'testnick',
                'avatar' => 'avatarhash',
            ],
            [
                'user' => [
                    'id' => '456',
                    'username' => 'botuser',
                    'avatar' => 'avatarhash',
                    'bot' => true,
                ],
                'nick' => 'botnick',
                'avatar' => 'avatarhash',
            ],
        ]);

        dispatch(new PutDiscordMembersJob);

        $this->assertDatabaseHas('discord_users', [
            'discord_id' => '123',
            'username' => 'testuser',
            'nickname' => 'testnick',
            'avatar_hash' => 'avatarhash',
        ]);
        $this->assertDatabaseMissing('discord_users', [
            'discord_id' => '456',
            'username' => 'botuser',
            'nickname' => 'botnick',
            'avatar_hash' => 'avatarhash',
        ]);
        $this->assertDatabaseCount('discord_users', 1);
    }
}
