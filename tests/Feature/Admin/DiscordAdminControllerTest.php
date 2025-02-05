<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\DiscordAdminController;
use App\Models\User;
use App\Services\Discord\BotRedirectBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DiscordAdminController::class)]
#[CoversClass(BotRedirectBuilder::class)]
class DiscordAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_connect_redirects(): void
    {
        $user = User::factory()->isAdmin()->create();
        $this->actingAs($user);

        $response = $this->get(route('discord.connect'));

        $response->assertRedirectContains('discord.com/api/oauth2/authorize');
    }

    public function test_connect_only_accessible_to_admins(): void
    {
        $user = User::factory()->isMember()->create();
        $this->actingAs($user);

        $response = $this->get(route('discord.connect'));

        $response->assertForbidden();
    }
}
