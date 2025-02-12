<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\DiscordUser;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MemberShowController::class)]
class MemberShowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_member_details(): void
    {
        $this->asAdminUser();

        $member = Member::factory()->create();

        $response = $this->get(route('member.show', [$member->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Show')
            ->where('member.id', $member->id)
            ->where('member.name', $member->name)
        );
    }

    public function test_it_shows_discord_user_if_has_one(): void
    {
        $this->asAdminUser();

        $member = Member::factory()
            ->has(DiscordUser::factory())
            ->create();

        $response = $this->get(route('member.show', [$member->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Show')
            ->where('member.id', $member->id)
            ->where('member.name', $member->name)
            ->has('member.discordUser', fn ($discordUser) => $discordUser
                ->where('id', $member->discordUser->id)
                ->where('username', $member->discordUser->username)
                ->etc()
            )
        );
    }

    public function test_it_shows_null_discord_user_if_does_not_have_one(): void
    {
        $this->asAdminUser();

        $member = Member::factory()->create();

        $response = $this->get(route('member.show', [$member->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Show')
            ->where('member.id', $member->id)
            ->where('member.name', $member->name)
            ->where('member.discordUser', null)
        );
    }

    public function test_it_returns_403_if_user_is_not_admin_or_not_associated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $member = Member::factory()->create();

        $response = $this->get(route('member.show', [$member->id]));

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

        $response = $this->get(route('member.show', [$member->id]));

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

        $response = $this->get(route('member.show', [$member->id]));

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

        $response = $this->get(route('member.show', [$invalidUuid]));

        $response->assertStatus(404);
    }
}
