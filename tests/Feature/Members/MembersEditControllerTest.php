<?php

namespace Tests\Feature\Members;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MembersEditController::class)]
class MembersEditControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_edit_member_form(): void
    {
        $this->asAdminUser();

        $member = Member::factory()->create();

        $response = $this->get("/members/{$member->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Edit')
            ->has('member', fn ($member) => $member
                ->where('id', $member->id)
                ->where('name', $member->name)
            )
        );
    }

    public function test_associated_user_can_view_edit_member_form(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get("/members/{$member->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Edit')
            ->has('member', fn ($member) => $member
                ->where('id', $member->id)
                ->where('name', $member->name)
            )
        );
    }

    public function test_non_admin_and_non_associated_user_cannot_view_edit_member_form(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $member = Member::factory()->create();

        $response = $this->get("/members/{$member->id}/edit");

        $response->assertStatus(403);
    }
}
