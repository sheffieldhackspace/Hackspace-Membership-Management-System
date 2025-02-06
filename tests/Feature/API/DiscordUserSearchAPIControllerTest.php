<?php

namespace Tests\Feature\API;

use App\Http\Controllers\API\DiscordUserSearchAPIController;
use App\Http\Requests\DiscordUsers\DiscordUserSearchRequest;
use App\Models\DiscordUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DiscordUserSearchAPIController::class)]
#[CoversClass(DiscordUserSearchRequest::class)]
class DiscordUserSearchAPIControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->adminUser = User::factory()->isAdmin()->create();
    }

    public function test_admin_can_search_discord_members_by_name()
    {
        // Create some Discord members
        $user1 = DiscordUser::factory()->create([
            'username' => 'testuser1',
            'nickname' => 'nickname1',
            'avatar_hash' => 'avatar1',
        ]);

        $user2 = DiscordUser::factory()->create([
            'username' => 'testuser2',
            'nickname' => 'nickname2',
            'avatar_hash' => 'avatar2',
        ]);

        DiscordUser::factory()->create([
            'username' => 'anotheruser',
            'nickname' => 'anothernick',
            'avatar_hash' => 'avatar3',
        ]);

        // Search for members with 'test' in their name
        $response = $this->actingAs($this->adminUser)->getJson(route('api.discord-members.search', ['term' => 'test']));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment([
            'username' => $user1->username,
            'nickname' => $user1->nickname,
            'avatar' => $user1->getAvatar(),
        ]);
        $response->assertJsonFragment([
            'username' => $user2->username,
            'nickname' => $user2->nickname,
            'avatar' => $user2->getAvatar(),
        ]);
    }

    public function test_admin_can_search_discord_members_returns_max_5_results_by_default()
    {
        // Create 6 Discord members with similar names
        for ($i = 1; $i <= 6; $i++) {
            DiscordUser::factory()->create([
                'username' => "user{$i}",
                'nickname' => "nick{$i}",
                'avatar_hash' => "avatar{$i}.png",
            ]);
        }

        // Search for members with 'user' in their name
        $response = $this->actingAs($this->adminUser)->getJson(route('api.discord-members.search', ['term' => 'user']));

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    public function test_admin_can_search_discord_members_returns_only_the_passed_limit_of_results()
    {
        // Create 6 Discord members with similar names
        for ($i = 1; $i <= 4; $i++) {
            DiscordUser::factory()->create([
                'username' => "user{$i}",
                'nickname' => "nick{$i}",
                'avatar_hash' => "avatar{$i}.png",
            ]);
        }

        // Search for members with 'user' in their name
        $response = $this->actingAs($this->adminUser)->getJson(route('api.discord-members.search', ['term' => 'user', 'limit' => 3]));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_admin_can_search_discord_members_by_nickname()
    {
        // Create a Discord member
        $user = DiscordUser::factory()->create([
            'username' => 'someuser',
            'nickname' => 'uniquenick',
            'avatar_hash' => 'avatar.png',
        ]);

        // Search for members with 'unique' in their nickname
        $response = $this->actingAs($this->adminUser)->getJson(route('api.discord-members.search', ['term' => 'unique']));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'username' => $user->username,
            'nickname' => $user->nickname,
            'avatar' => $user->getAvatar(),
        ]);
    }

    public function test_non_admin_cannot_search_discord_members()
    {
        // Create a non-admin user
        $user = User::factory()->create();

        // Attempt to search for members
        $response = $this->actingAs($user)->getJson(route('api.discord-members.search', ['term' => 'test']));

        $response->assertStatus(403);
    }
}
