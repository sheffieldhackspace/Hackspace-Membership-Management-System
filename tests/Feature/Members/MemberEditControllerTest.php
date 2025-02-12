<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\DiscordUser;
use App\Models\Member;
use App\Models\PostalAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $member->setMembershipType(MembershipType::MEMBER);

        $response = $this->get(route('member.update', [$member->id]));

        $response->assertStatus(403);
    }

    public function test_invalid_uuid_returns_404(): void
    {
        $this->asAdminUser();

        $invalidUuid = 'invalid-uuid';

        $response = $this->get(route('member.edit', [$invalidUuid]));

        $response->assertStatus(404);
    }

    public function test_controller_passes_member_details(): void
    {
        $this->asAdminUser();

        $member = Member::factory()
            ->isKeyholder()
            ->has(PostalAddress::factory())
            ->create();

        $response = $this->get(route('member.edit', [$member->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Edit')
            ->where('member.id', $member->id)
            ->where('member.name', $member->name)
            ->where('member.knownAs', $member->known_as)
            ->where('member.membershipType.label', $member->getMembershipType()->label())
            ->where('member.membershipType.value', $member->getMembershipType()->value)
            ->where('member.hasActiveMembership', $member->getHasActiveMembership())
            ->where('member.joiningDate', $member->getJoiningDate()->toDateString())
            ->has('member.emailAddresses', 1)
            ->where('member.emailAddresses.0.emailAddress', $member->emailAddresses->first()->email_address)
            ->has('member.postalAddress', fn ($postalAddress) => $postalAddress
                ->where('id', $member->postalAddress->id)
                ->where('memberId', $member->id)
                ->where('line1', $member->postalAddress->line_1)
                ->where('line2', $member->postalAddress->line_2)
                ->where('line3', $member->postalAddress->line_3)
                ->where('city', $member->postalAddress->city)
                ->where('county', $member->postalAddress->county)
                ->where('postcode', $member->postalAddress->postcode)
            )
            ->where('member.trusteeHistory', [])
            ->has('member.membershipHistory', 2)
        );
    }

    public function test_controller_passes_discord_user_if_has_one(): void
    {
        $this->asAdminUser();

        $member = Member::factory()
            ->has(DiscordUser::factory())
            ->create();

        $response = $this->get(route('member.edit', [$member->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Edit')
            ->has('member.discordUser', fn ($discordUser) => $discordUser
                ->where('id', $member->discordUser->id)
                ->where('username', $member->discordUser->username)
                ->etc()
            )
        );
    }

    public function test_controller_passes_null_discord_user_if_does_not_have_one(): void
    {
        $this->asAdminUser();

        $member = Member::factory()->create();

        $response = $this->get(route('member.edit', [$member->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Show')
            ->where('member.discordUser', null)
        );
    }
}
