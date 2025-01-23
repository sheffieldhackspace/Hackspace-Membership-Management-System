<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MemberUpdateController::class)]
class MemberUpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_member(): void
    {
        $this->asAdminUser();

        $member = Member::factory()->create();
        $data = ['name' => 'Updated Name'];

        $response = $this->patch("/members/{$member->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Updated Name']);
    }

    public function test_associated_user_can_update_member(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $data = ['name' => 'Updated Name'];

        $response = $this->patch("/members/{$member->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Updated Name']);
    }

    public function test_non_admin_and_non_associated_user_cannot_update_member(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $member = Member::factory()->create();
        $data = ['name' => 'Updated Name'];

        $response = $this->patch("/members/{$member->id}", $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('members', ['id' => $member->id, 'name' => 'Updated Name']);
    }

    public function test_non_admin_cannot_change_membership_type(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()
            ->has(MembershipHistory::factory(['membership_type' => MembershipType::MEMBER]))
            ->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $data = ['name' => 'Updated Name', 'membership_type' => MembershipType::KEYHOLDER->value];

        $response = $this->patch("/members/{$member->id}", $data);

        $response->assertStatus(403);
        $this->assertDatabaseHas('members', ['id' => $member->id, 'membership_type' => MembershipType::MEMBER]);
    }

    public function test_invalid_uuid_returns_404(): void
    {
        $this->asAdminUser();

        $invalidUuid = 'invalid-uuid';
        $data = ['name' => 'Updated Name'];

        $response = $this->patch("/members/{$invalidUuid}", $data);

        $response->assertStatus(404);
    }
}
