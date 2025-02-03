<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\DiscordController;
use App\Models\DiscordUser;
use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\User;
use App\Providers\DiscordServiceProvider;
use App\Services\Discord\DiscordProvider;
use App\Services\Discord\DiscordService;
use App\Services\Discord\SocialiteDiscordUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DiscordController::class)]
#[CoversClass(DiscordServiceProvider::class)]
#[CoversClass(DiscordProvider::class)]
#[CoversClass(SocialiteDiscordUser::class)]
#[CoversClass(DiscordService::class)]
class DiscordControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_redirect_redirects_correctly(): void
    {
        $response = $this->get(route('discord.redirect'));

        $response->assertRedirectContains(urlencode(route('discord.callback')));
        $response->assertRedirectContains('scope=email+identify+guilds');
    }

    public function test_verified_discord_user_in_the_guild_without_matching_user_or_member_is_authenticated_with_new_user(): void
    {
        Carbon::setTestNow(now());

        $discordId = '3845945134875743875';
        User::factory()
            ->has(
                DiscordUser::factory([
                    'discord_id' => $discordId,
                ])
            )->create();

        $this->fakeOAuthRequests([
            'id' => $discordId,
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $user = User::whereDiscordId($discordId)->first();
        $this->assertNotNull($user, 'User should have been created');

        $response->assertRedirectToRoute('dashboard');
        $this->assertAuthenticatedAs($user);

    }

    public function test_verified_discord_user_in_the_guild_with_discord_id_on_user_is_authenticated(): void
    {
        Carbon::setTestNow(now());

        $user = User::factory()
            ->has(
                DiscordUser::factory([
                    'discord_id' => '3845945134875743875',
                ])
            )->create();

        $this->fakeOAuthRequests([
            'id' => $user->discordUser->discord_id,
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

    }

    public function test_discord_user_in_the_guild_with_discord_id_on_member_and_not_user_is_authenticated(): void
    {
        Carbon::setTestNow(now());

        $discordId = '3845945134875743875';
        $member = Member::factory()
            ->has(
                DiscordUser::factory([
                    'discord_id' => $discordId,
                ])
            )
            ->isMember()
            ->create();

        $this->fakeOAuthRequests([
            'id' => $discordId,
            'verified' => true,
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $user = User::whereDiscordId($discordId)->first();
        $this->assertNotNull($user, 'User should have been created');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('discord_users', [
            'discord_id' => $discordId,
            'user_id' => $user->id,
            'member_id' => $member->id,
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'id' => $member->id,
        ]);
    }

    public function test_verified_discord_user_in_the_guild_without_discord_id_on_user_but_matching_member_is_authenticated(): void
    {
        Carbon::setTestNow(now());

        $discordId = '3845945134875743875';
        $emailAddress = $this->faker->email;
        $member = Member::factory()
            ->has(EmailAddress::factory(['email_address' => $emailAddress, 'verified_at' => null, 'is_primary' => true]))
            ->create();

        $this->fakeOAuthRequests([
            'id' => $discordId,
            'email' => $emailAddress,
            'verified' => true,
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $user = User::whereDiscordId($discordId)->first();
        $this->assertNotNull($user, 'User should have been created');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('discord_users', [
            'discord_id' => $discordId,
            'user_id' => $user->id,
            'member_id' => $member->id,
        ]);

        $this->assertDatabaseHas('email_addresses', [
            'email_address' => $emailAddress,
            'verified_at' => now(),
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => $user->id,
            'id' => $member->id,
        ]);

    }

    public function test_unverified_discord_user_in_the_guild_without_discord_id_on_user_but_with_matching_member_is_prompted_to_verify(): void
    {
        Carbon::setTestNow(now());

        $discordId = '3845945134875743875';
        $emailAddress = $this->faker->email;
        $member = Member::factory()
            ->has(EmailAddress::factory(['email_address' => $emailAddress, 'verified_at' => null, 'is_primary' => true]))
            ->create();
        $this->fakeOAuthRequests([
            'id' => $discordId,
            'email' => $emailAddress,
            'verified' => false,
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $user = User::whereDiscordId($discordId)->first();
        $this->assertNotNull($user, 'User should have been created');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('discord_users', [
            'discord_id' => $discordId,
            'user_id' => $user->id,
            'member_id' => null,
        ]);

        $this->assertDatabaseHas('email_addresses', [
            'email_address' => $emailAddress,
            'verified_at' => null,
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => null,
            'id' => $member->id,
        ]);

    }

    public function test_discord_user_in_the_guild_without_discord_id_on_user_and_without_matching_member_is_prompted_to_send_match_emails(): void
    {
        Carbon::setTestNow(now());

        $discordId = '3845945134875743875';
        $emailAddress = $this->faker->email;
        $member = Member::factory()
            ->has(EmailAddress::factory(['email_address' => $emailAddress, 'verified_at' => null, 'is_primary' => true]))
            ->create();
        $this->fakeOAuthRequests([
            'id' => $discordId,
            'email' => $emailAddress,
            'verified' => false,
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $user = User::whereDiscordId($discordId)->first();
        $this->assertNotNull($user, 'User should have been created');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('discord_users', [
            'discord_id' => $discordId,
            'user_id' => $user->id,
            'member_id' => null,
        ]);

        $this->assertDatabaseHas('email_addresses', [
            'email_address' => $emailAddress,
            'verified_at' => null,
        ]);

        $this->assertDatabaseHas('members', [
            'user_id' => null,
            'id' => $member->id,
        ]);

    }

    public function test_verified_and_avatar_has_updated_on_login(): void
    {
        Carbon::setTestNow(now());

        $discordId = '3845945134875743875';
        $user = User::factory()
            ->has(
                DiscordUser::factory([
                    'discord_id' => $discordId,
                    'avatar_hash' => 'old_avatar_hash',
                    'verified' => false,
                ])
            )->create();

        $this->fakeOAuthRequests([
            'id' => $discordId,
            'avatar' => 'new_avatar_hash',
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertRedirectToRoute('dashboard');
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('discord_users', [
            'discord_id' => $discordId,
            'avatar_hash' => 'new_avatar_hash',
            'verified' => true,
        ]);

    }

    public function test_discord_user_not_in_the_guild_cannot_login(): void
    {
        $this->fakeOAuthRequests([], [
            [
                'id' => '32434665765',
                'name' => 'Test Guild',
                'icon' => 'icon',
                'owner' => false,
                'permissions' => 0,
                'permissions_new' => '0',
                'features' => [],
            ],
        ]);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertUnauthorized();
    }

    public function fakeOAuthRequests(array $user = [], array $guilds = []): void
    {
        Http::preventStrayRequests();

        if (count($guilds) === 0) {
            $guilds = [
                0 => [
                    'id' => config('services.discord.guild_id'),
                    'name' => 'Test Guild',
                    'icon' => 'icon',
                    'owner' => false,
                    'permissions' => 0,
                    'permissions_new' => '0',
                    'features' => [],
                ],
            ];
        }

        Http::fake([
            'discord.com/api/oauth2/token' => Http::response([
                'access_token' => '6qrZcUqja7812RVdnEKjpzOL4CvHBFG',
                'token_type' => 'Bearer',
                'expires_in' => 604800,
                'refresh_token' => 'D43f5y0ahjqew82jZ4NViEr2YafMKhue',
                'scope' => 'identify',
            ]),
            'discord.com/api/users/@me' => Http::response([
                'id' => '80351110224678912',
                'username' => 'name',
                'global_name' => 'nickname',
                'email' => $this->faker->email,
                'avatar' => '8342729096ea3675442027381ff50dfe',
                'verified' => true,
                ...$user,
            ]),
            'discord.com/api/users/@me/guilds' => Http::response($guilds),
        ]);

        $this->session(['state' => 'test_state']);

    }
}
