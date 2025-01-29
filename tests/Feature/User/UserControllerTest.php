<?php

namespace Tests\Feature\User;

use App\Models\User;
use Config;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\UserController::class)]
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_any_profile_page(): void
    {
        $adminUser = User::factory()->isAdmin()->create();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($adminUser)
            ->get(route('user.edit', [$user->id]));

        $response->assertOk();
    }

    public function test_user_can_view_their_profile_page(): void
    {
        $user = User::factory()->isPWUser()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('user.edit', [$user->id]));

        $response->assertOk();
    }

    public function test_user_cant_view_other_profile_page(): void
    {
        $pwUser = User::factory()->isPWUser()->create();
        $user = User::factory()->create();

        $response = $this
            ->actingAs($pwUser)
            ->get(route('user.edit', [$user->id]));

        $response->assertForbidden();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('user.update', [$user->id]), [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('user.edit', [$user->id]));

        $user->refresh();

        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('user.update', [$user->id]), [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('user.edit', [$user->id]));

        $this->assertNotNull($user->refresh()->email_verified_at);
    }
}
