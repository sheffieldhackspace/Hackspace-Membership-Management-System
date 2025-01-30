<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\DiscordController;
use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\User;
use App\Providers\DiscordServiceProvider;
use App\Services\Discord\DiscordProvider;
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
class DiscordControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_redirect_redirects_correctly(): void
    {
        $response = $this->get(route('discord.redirect'));

        $response->assertRedirect();
        $response->assertRedirectContains(urlencode(route('discord.callback')));
        $response->assertRedirectContains('scope=email+identify+guilds');
    }

    public function test_verified_discord_user_in_the_guild_with_discord_id_on_user(): void
    {
        Carbon::setTestNow(now());

        $user = User::factory(['discord_id' => 'asdasd1231'])->create();

        $this->fakeOAuthRequests([
            'id' => $user->discord_id,
        ], [
            0 => [
                'id' => config('services.discord.guild_id'),
                'name' => 'Test Guild',
                'icon' => 'icon',
                'owner' => false,
                'permissions' => 0,
                'permissions_new' => '0',
                'features' => [],
            ],
        ]);

        $this->session(['state' => 'test_state']);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertOk();
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

    }

    public function test_verified_discord_user_in_the_guild_without_discord_id_on_user_but_matching_member_is_authenticated(): void
    {
        Carbon::setTestNow(now());

        $emailAddress = $this->faker->email;

        $user = User::factory()
            ->has(Member::factory()
                ->has(EmailAddress::factory(['email_address' => $emailAddress, 'verified_at' => null, 'is_primary' => true]))
            )
            ->create();
        $this->fakeOAuthRequests([
            'email' => $emailAddress,
            'verified' => true,
        ], [
            0 => [
                'id' => config('services.discord.guild_id'),
                'name' => 'Test Guild',
                'icon' => 'icon',
                'owner' => false,
                'permissions' => 0,
                'permissions_new' => '0',
                'features' => [],
            ],
        ]);

        $this->session(['state' => 'test_state']);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertOk();
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('email_addresses', [
            'email_address' => $emailAddress,
            'verified_at' => now(),
        ]);

    }

    public function test_unverified_discord_user_in_the_guild_without_discord_id_on_user_but_with_matching_member_is_prompted_to_verify(): void
    {
        Carbon::setTestNow(now());

        $emailAddress = $this->faker->email;

        $user = User::factory()
            ->has(Member::factory()
                ->has(EmailAddress::factory(['email_address' => $emailAddress, 'verified_at' => null, 'is_primary' => true]))
            )
            ->create();
        $this->fakeOAuthRequests([
            'email' => $emailAddress,
            'verified' => false,
        ], [
            0 => [
                'id' => config('services.discord.guild_id'),
                'name' => 'Test Guild',
                'icon' => 'icon',
                'owner' => false,
                'permissions' => 0,
                'permissions_new' => '0',
                'features' => [],
            ],
        ]);

        $this->session(['state' => 'test_state']);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertOk();
        $response->assertRedirect(route('discord.link.user'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('email_addresses', [
            'email_address' => $emailAddress,
            'verified_at' => null,
        ]);

    }

    public function test_discord_user_in_the_guild_without_discord_id_on_user_and_without_matching_member_is_prompted_to_send_match_emails(): void
    {
        Carbon::setTestNow(now());

        $emailAddress = $this->faker->email;

        $user = User::factory()
            ->has(Member::factory()
                ->has(EmailAddress::factory(['email_address' => $emailAddress, 'verified_at' => null, 'is_primary' => true]))
            )
            ->create();
        $this->fakeOAuthRequests([
            'email' => $emailAddress,
            'verified' => false,
        ], [
            0 => [
                'id' => config('services.discord.guild_id'),
                'name' => 'Test Guild',
                'icon' => 'icon',
                'owner' => false,
                'permissions' => 0,
                'permissions_new' => '0',
                'features' => [],
            ],
        ]);

        $this->session(['state' => 'test_state']);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertOk();
        $response->assertRedirect(route('discord.link.user'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('email_addresses', [
            'email_address' => $emailAddress,
            'verified_at' => null,
        ]);

    }

    public function test_discord_user_not_in_the_guild_cannot_login(): void
    {
        $this->fakeOAuthRequests();

        $this->session(['state' => 'test_state']);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertStatus(403);
    }

    public function fakeOAuthRequests(array $user = [], array $guilds = []): void
    {
        Http::preventStrayRequests();

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
            'discord.com/api/users/@me/guilds' => Http::response([
                0 => [
                    'id' => '197038439483310086',
                    'name' => 'Test Guild',
                    'icon' => 'icon',
                    'owner' => false,
                    'permissions' => 0,
                    'permissions_new' => '0',
                    'features' => [],
                ],
                ...$guilds,
            ]),
        ]);
    }
}
