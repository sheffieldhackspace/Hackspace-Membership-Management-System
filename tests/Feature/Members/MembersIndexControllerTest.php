<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\Member;
use App\Models\MembershipHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MembersIndexController::class)]
class MembersIndexControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_a_list_of_members(): void
    {
        $this->asAdminUser();

        Member::factory()->count(3)->create();

        $response = $this->get('/members');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 3)
        );
    }

    public function test_it_filters_members_by_search_term(): void
    {
        $this->asAdminUser();

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
        $this->asAdminUser();

        Member::factory()->has(MembershipHistory::factory(['membership_type' => MembershipType::MEMBER]))->create();
        Member::factory()->has(MembershipHistory::factory(['membership_type' => MembershipType::KEYHOLDER]))->create();

        $response = $this->get('/members?membership_type=Keyholder');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Members/Index')
            ->has('members.data', 1)
            ->where('members.data.0.membershipType', MembershipType::KEYHOLDER)
        );
    }

    public function test_it_redirects_unauthenticated_users(): void
    {
        $response = $this->get('/members');

        $response->assertRedirect('/login');
    }
}
