<?php

namespace Tests\Feature\Members;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MemberStoreController::class)]
class MemberStoreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_member(): void
    {
        $this->markTestIncomplete('This functionality has not been implemented yet.');

        $user = User::factory()->isAdmin()->create();
        $this->actingAs($user);

        $data = ['name' => 'New Member', 'email' => 'newmember@example.com'];

        $response = $this->post(route('member.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('members', ['name' => 'New Member', 'email' => 'newmember@example.com']);

    }

    public function test_non_admin_cannot_create_member(): void
    {
        $this->markTestIncomplete('This functionality has not been implemented yet.');

        $user = User::factory()->create();
        $this->actingAs($user);

        $data = ['name' => 'New Member', 'email' => 'newmember@example.com'];

        $response = $this->post(route('member.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('members', ['name' => 'New Member', 'email' => 'newmember@example.com']);

        $this->markTestIncomplete('This functionality has not been implemented yet.');
    }
}
