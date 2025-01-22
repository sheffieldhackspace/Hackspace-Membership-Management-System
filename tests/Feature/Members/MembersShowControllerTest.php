<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MembersShowController::class)]
class MembersShowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_member_details(): void
    {
        $this->asAdminUser();

        $member = Member::factory()->create();

        $response = $this->get("/member/{$member->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Show')
            ->where('member.id', $member->id)
            ->where('member.name', $member->name)
        );
    }

    public function test_it_returns_403_if_user_is_not_admin_or_not_associated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $member = Member::factory()->create();

        $response = $this->get("/member/{$member->id}");

        $response->assertStatus(403);
    }

    public function test_it_shows_member_details_if_user_is_associated(): void
    {
        $user = User::factory()->create();
        /** @var Member $member */
        $member = Member::factory()
            ->has(MembershipHistory::factory(['membership_type' => MembershipType::MEMBER]))
            ->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get("/member/{$member->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Show')
            ->where('member.id', $member->id)
            ->where('member.name', $member->name)
            ->where('member.knownAs', $member->known_as)
        );
    }

    public function test_it_shows_member_details_if_user_is_admin(): void
    {
        $this->asAdminUser();
        /** @var Member $member */
        $member = Member::factory()->create();

        $response = $this->get("/member/{$member->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Show')
            ->where('member.id', $member->id)
            ->where('member.name', $member->name)
        );
    }

    public function test_invalid_uuid_returns_404(): void
    {
        $this->asAdminUser();

        $invalidUuid = 'invalid-uuid';

        $response = $this->get("/member/{$invalidUuid}");

        $response->assertStatus(404);
    }
}
