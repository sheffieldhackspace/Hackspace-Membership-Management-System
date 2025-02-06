<?php

namespace Tests\Feature\Middleware;

use App\Enums\MembershipType;
use App\Http\Middleware\PermissionMiddleware;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(PermissionMiddleware::class)]
class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Define a test route that uses the RoleMiddleware
        Route::middleware(['permission:view-pw-member-report|create-member'])->get('/test-role-middleware', function () {
            return response()->json(['message' => 'Access granted']);
        });
    }

    public function test_user_with_required_role_on_user_can_access_route(): void
    {
        $user = User::factory()->create();
        $user->assignRole('pw-user');
        $this->actingAs($user);

        $response = $this->get('/test-role-middleware');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Access granted']);
    }

    public function test_user_with_required_role_on_member_can_access_route(): void
    {
        $user = User::factory([])
            ->has(Member::factory([
                'name' => 'Admin User',
                'known_as' => 'Admin',
            ])
                ->has(MembershipHistory::factory([
                    'membership_type' => MembershipType::KEYHOLDER,
                ]))
                ->isTrustee()
            )->create();
        $this->actingAs($user);

        $response = $this->get('/test-role-middleware');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Access granted']);
    }

    public function test_user_without_required_role_cannot_access_route(): void
    {
        $user = User::factory([])
            ->has(Member::factory([
                'name' => 'Member User',
                'known_as' => 'Member',
            ])
                ->has(MembershipHistory::factory([
                    'membership_type' => MembershipType::MEMBER,
                ]))
            )->create();
        $this->actingAs($user);

        $response = $this->get('/test-role-middleware');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_route(): void
    {
        $response = $this->get('/test-role-middleware');

        $response->assertStatus(403);
    }
}
