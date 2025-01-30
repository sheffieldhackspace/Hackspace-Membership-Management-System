<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\DiscordController;
use App\Providers\DiscordServiceProvider;
use App\Services\Discord\DiscordProvider;
use App\Services\Discord\DiscordUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DiscordController::class)]
#[CoversClass(DiscordServiceProvider::class)]
#[CoversClass(DiscordProvider::class)]
#[CoversClass(DiscordUser::class)]
class DiscordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_redirects_correctly(): void
    {
        $response = $this->get(route('discord.redirect'));

        $response->assertRedirect();
        $response->assertRedirectContains(urlencode(route('discord.callback')));
        $response->assertRedirectContains('scope=email+identify+guilds');
    }

    public function test_discord_user_not_in_the_guild_cannot_login(): void
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
                'email' => 'icon',
                'avatar' => '8342729096ea3675442027381ff50dfe',
                'verified' => true,
            ]),
            'discord.com/api/users/@me/guilds' => Http::response([
                [
                    'id' => '197038439483310086',
                    'name' => 'Test Guild',
                    'icon' => 'icon',
                    'owner' => false,
                    'permissions' => 0,
                    'permissions_new' => '0',
                    'features' => [],
                ],
            ]),
        ]);

        $this->session(['state' => 'test_state']);

        $response = $this->get(route('discord.callback', ['code' => 'test_code', 'state' => 'test_state']));

        $response->assertStatus(403);

    }
}
