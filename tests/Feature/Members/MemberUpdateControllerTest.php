<?php

namespace Tests\Feature\Members;

use App\Enums\MembershipType;
use App\Models\EmailAddress;
use App\Models\Member;
use App\Models\MembershipHistory;
use App\Models\PostalAddress;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(\App\Http\Controllers\Members\MemberUpdateController::class)]
#[CoversClass(\App\Http\Requests\Members\MembersUpdateRequest::class)]
#[CoversClass(\App\Rules\OnePrimaryEmailAddress::class)]
class MemberUpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('provideNameUserMemberData')]
    public function test_can_update_members_name(Closure $getUserAndMember): void
    {
        /**
         * @var User $user
         * @var Member $member
         */
        [$user, $member] = $getUserAndMember();
        $this->actingAs($user);

        $data = $this->getData($member, ['name' => 'Updated Name', 'knownAs' => 'Updated Known As']);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('members', ['id' => $member->id, 'name' => 'Updated Name', 'known_as' => 'Updated Known As']);
    }

    #[DataProvider('providePostalAddressUserMemberData')]
    public function test_can_create_and_update_a_postal_address_for_the_member(Closure $getUserAndMember): void
    {
        /**
         * @var User $user
         * @var Member $member
         */
        [$user, $member] = $getUserAndMember();
        $this->actingAs($user);

        $address = [
            'line1' => 'Line 1',
            'line2' => 'Line 2',
            'line3' => 'Line 3',
            'city' => 'City',
            'county' => 'County',
            'postcode' => 'Postcode',
        ];

        $data = $this->getData($member,
            [
                'postalAddress' => $address,
            ]);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('members', ['id' => $member->id]);
        $this->assertDatabaseHas('postal_addresses', [
            'member_id' => $member->id,
            'line_1' => $address['line1'],
            'line_2' => $address['line2'],
            'line_3' => $address['line3'],
            'city' => $address['city'],
            'county' => $address['county'],
            'postcode' => $address['postcode'],
        ]);
    }

    #[DataProvider('providePostalAddressUserMemberData')]
    public function test_wont_create_or_update_a_postal_address_for_the_member_with_invalid_data(Closure $getUserAndMember): void
    {
        /**
         * @var User $user
         * @var Member $member
         */
        [$user, $member] = $getUserAndMember();
        $memberAddress = $member->postalAddress;
        $this->actingAs($user);

        $address = [
            'line1' => '',
            'line2' => 'Line 2',
            'line3' => 'Line 3',
            'city' => 'City',
            'county' => 'County',
            'postcode' => '',
        ];

        $data = $this->getData($member,
            [
                'postalAddress' => $address,
            ]);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertSessionHasErrors(['postalAddress.line1', 'postalAddress.postcode']);
        if($memberAddress) {
            $this->assertDatabaseHas('postal_addresses', [
                'member_id' => $member->id,
                'line_1' => $memberAddress->line_1,
                'line_2' => $memberAddress->line_2,
                'line_3' => $memberAddress->line_3,
                'city' => $memberAddress->city,
                'county' => $memberAddress->county,
                'postcode' =>$memberAddress->postcode,
            ]);
        }
    }

    public function test_non_admin_and_non_associated_user_cannot_update_member(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $member = Member::factory()->isMember()->create();
        $member->setMembershipType(MembershipType::MEMBER);

        $data = $this->getData($member, ['name' => 'Updated Name']);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('members', ['id' => $member->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_change_membership_type(): void
    {
        $user = User::factory()->isAdmin()->create();
        $member = Member::factory()
            ->isMember()
            ->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $data = $this->getData($member, ['membershipType' => MembershipType::KEYHOLDER->value]);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('membership_histories', ['member_id' => $member->id, 'membership_type' => MembershipType::KEYHOLDER->value]);
    }

    public function test_non_admin_cannot_change_membership_type(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()
            ->isMember()
            ->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $data = $this->getData($member, ['membershipType' => MembershipType::KEYHOLDER->value]);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('membership_histories', ['member_id' => $member->id, 'membership_type' => MembershipType::KEYHOLDER->value]);
    }

    public function test_admin_can_create_new_trustee(): void
    {
        Carbon::setTestNow(now()->subYear());

        $user = User::factory()->isAdmin()->create();
        $member = Member::factory()
            ->isMember()
            ->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $data = $this->getData($member, ['trustee' => true]);

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trustee_histories', ['member_id' => $member->id, 'elected_at' => $now]);
    }

    public function test_admin_can_resign_existing_trustee(): void
    {
        $user = User::factory()->isAdmin()->create();
        $member = Member::factory()
            ->isMember()
            ->isTrustee()
            ->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $data = $this->getData($member, ['trustee' => false]);

        $resignedAt = Carbon::now();
        Carbon::setTestNow($resignedAt);

        $response = $this->patch(route('member.update', [$member->id]), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('trustee_histories', ['member_id' => $member->id, 'resigned_at' => $resignedAt]);
    }

    public function test_non_admin_cannot_change_trustee_status(): void
    {
        $user = User::factory()->create();
        $member = Member::factory()
            ->isMember()
            ->create(['user_id' => $user->id]);
        $this->actingAs($user);
        $data = $this->getData($member, ['trustee' => true]);

        $resignedAt = Carbon::now();
        Carbon::setTestNow($resignedAt);

        $response = $this->patch(route('member.update', [$member->id]), $data);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('trustee_histories', ['member_id' => $member->id, 'resigned_at' => $resignedAt]);
    }

    public function test_invalid_uuid_returns_404(): void
    {
        $this->asAdminUser();

        $invalidUuid = 'invalid-uuid';
        $data = ['name' => 'Updated Name'];

        $response = $this->patch(route('member.update', [$invalidUuid]), $data);

        $response->assertStatus(404);
    }

    static public function provideNameUserMemberData(): iterable
    {
        Yield [
            function (): array {
                return [
                    User::factory()->isAdmin()->create(),
                    Member::factory()->create(),
                ];
            },
        ];
        Yield [
            function (): array {
                $user = User::factory()->create();
                return [
                    $user,
                    Member::factory(['user_id' => $user->id])->isMember()->create(),
                ];
            },
        ];
    }

    static public function providePostalAddressUserMemberData(): iterable
    {
        Yield [
            function (): array {
                return [
                    User::factory()->isAdmin()->create(),
                    Member::factory()->create(),
                ];
            },
        ];
        Yield [
            function (): array {
                return [
                    User::factory()->isAdmin()->create(),
                    Member::factory()->has(PostalAddress::factory())->create(),
                ];
            },
        ];
        Yield [
            function (): array {
                $user = User::factory()->create();
                return [
                    $user,
                    Member::factory(['user_id' => $user->id])->isMember()->create(),
                ];
            },
        ];
        Yield [
            function (): array {
                $user = User::factory()->create();
                return [
                    $user,
                    Member::factory(['user_id' => $user->id])->isMember()->has(PostalAddress::factory())->create(),
                ];
            },
        ];
    }

    private function getData(Member $member, $data): array
    {

        return array_merge([
            'name' => $member->name,
            'knownAs' => $member->known_as,
            'emailAddresses' => $member->emailAddresses->map(function(EmailAddress $emailAddress){
                return [
                    'id' => $emailAddress->id,
                    'emailAddress' => $emailAddress->email_address,
                    'isPrimary' => $emailAddress->is_primary,
                ];
            })->toArray(),
            'membershipType' => $member->getMembershipType()->value,
            'trustee' => $member->getIsActiveTrustee(),
        ], $data);
    }
}
