<?php

namespace Tests\Feature\Members;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MemberCreateController::class)]
class MemberCreateControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @
     */
    public function test_admin_can_view_create_member_form(): void
    {
        $this->markTestIncomplete('This functionality has not been implemented yet.');

        $this->asAdminUser();

        $response = $this->get('/members/create');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Create')
        );

    }

    public function test_non_admin_cannot_view_create_member_form(): void
    {
        $this->markTestIncomplete('This functionality has not been implemented yet.');

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/members/create');

        $response->assertStatus(403);
    }
}
