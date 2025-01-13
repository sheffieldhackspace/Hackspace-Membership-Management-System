<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MembersStoreController::class)]
class MembersStoreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_member(): void
    {
        $this->asAdminUser();

        $data = ['name' => 'New Member', 'email' => 'newmember@example.com'];

        $response = $this->post('/members', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('members', ['name' => 'New Member', 'email' => 'newmember@example.com']);
    }

    public function test_non_admin_cannot_create_member(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = ['name' => 'New Member', 'email' => 'newmember@example.com'];

        $response = $this->post('/members', $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('members', ['name' => 'New Member', 'email' => 'newmember@example.com']);
    }
}
