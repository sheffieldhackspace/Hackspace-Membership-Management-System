<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MembersIndexController::class)]
class MembersIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_a_list_of_members(): void
    {
        $user = User::factory()->isAdmin()->create();
        $this->actingAs($user);

        Member::factory()->count(3)->create();

        $response = $this->get('/members');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 4)
        );
    }

    public function test_it_filters_members_by_search_term(): void
    {
        $user = User::factory()->isAdmin()->create();
        $this->actingAs($user);

        Member::factory()->create(['name' => 'John Doe']);
        Member::factory()->create(['name' => 'Jane Smith']);

        $response = $this->get('/members?search=John');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 1)
            ->where('members.data.0.name', 'John Doe')
        );
    }

    public function test_it_filters_members_by_membership_type(): void
    {
        $user = User::factory()->isAdmin()->create();
        $this->actingAs($user);

        Member::factory()->has(MembershipHistory::factory(['membership_type' => MembershipType::MEMBER]))->create();
        Member::factory()->has(MembershipHistory::factory(['membership_type' => MembershipType::UNPAIDMEMBER]))->create();

        $response = $this->get('/members?membership_type=unpaid-member');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 1)
            ->where('members.data.0.membershipType.label', MembershipType::UNPAIDMEMBER->label())
            ->where('members.data.0.membershipType.value', MembershipType::UNPAIDMEMBER->value)
        );
    }

    public function test_it_redirects_unauthenticated_users(): void
    {
        $response = $this->get('/members');

        $response->assertRedirect('/login');
    }
}
