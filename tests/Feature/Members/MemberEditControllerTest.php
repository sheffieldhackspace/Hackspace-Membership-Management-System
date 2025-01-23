<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MemberEditController::class)]
class MemberEditControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_edit_member_form(): void
    {
        $this->asAdminUser();

        $memberModel = Member::factory()->create();

        $response = $this->get(route('member.edit', [$memberModel->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Edit')
            ->has('member', fn ($member) => $member
                ->where('id', $memberModel->id)
                ->where('name', $memberModel->name)
                ->has('membershipType', fn ($membershipType) => $membershipType
                    ->where('value', $memberModel->getMembershipType()->value)
                    ->etc()
                )
                ->etc()
            )
        );
    }

    public function test_associated_user_can_view_edit_member_form(): void
    {
        $user = User::factory()->create();
        $memberModel = Member::factory(['user_id' => $user->id])->create();
        $memberModel->setMembershipType(MembershipType::MEMBER);

        $this->actingAs($user);

        $response = $this->get(route('member.edit', [$memberModel->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Edit')
            ->has('member', fn ($member) => $member
                ->where('id', $memberModel->id)
                ->where('name', $memberModel->name)
                ->has('membershipType', fn ($membershipType) => $membershipType
                    ->where('value', $memberModel->getMembershipType()->value)
                    ->etc()
                )
                ->etc()
            )
        );
    }

    public function test_non_admin_and_non_associated_user_cannot_view_edit_member_form(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $member = Member::factory()->create();

        $response = $this->get(route('member.edit', [$member->id]));

        $response->assertStatus(403);
    }
}
