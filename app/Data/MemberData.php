<?php

namespace App\Data;

use App\Models\Member;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class MemberData extends Data
{
    /**
     * @param  EmailAddressData[]|Lazy  $emailAddresses
     * @param  TrusteeHistoryData[]|Lazy  $trusteeHistory
     * @param  MembershipHistoryData[]|Lazy  $membershipHistory
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $knownAs,
        public MembershipTypeData $membershipType,
        public bool $hasActiveMembership,
        public ?string $joiningDate,
        public array|Lazy $emailAddresses,
        public PostalAddressData|Lazy $postalAddress,
        public array|Lazy $trusteeHistory,
        public array|Lazy $membershipHistory,
        public DiscordUserData|Lazy|null $discordUser,
    ) {}

    public static function fromModel(Member $member): self
    {
        return new self(
            id: $member->id,
            name: $member->name,
            knownAs: $member->known_as,
            membershipType: MembershipTypeData::fromEnum($member->getMembershipType()),
            hasActiveMembership: $member->getHasActiveMembership(),
            joiningDate: $member->getJoiningDate() ? $member->getJoiningDate()->toDateString() : null,
            emailAddresses: Lazy::whenLoaded(
                'emailAddresses',
                $member,
                fn () => $member->emailAddresses->map(fn ($emailAddress) => EmailAddressData::fromModel($emailAddress))
            ),
            postalAddress: Lazy::whenLoaded(
                'postalAddress',
                $member,
                fn () => PostalAddressData::fromModel($member->postalAddress)
            ),
            trusteeHistory: Lazy::whenLoaded(
                'trusteeHistory',
                $member,
                fn () => $member->trusteeHistory->map(fn ($trusteeHistory) => TrusteeHistoryData::fromModel($trusteeHistory))
            ),
            membershipHistory: Lazy::whenLoaded(
                'membershipHistory',
                $member,
                fn () => $member->membershipHistory->map(fn ($membershipHistory) => MembershipHistoryData::fromModel($membershipHistory))
            ),
            discordUser: Lazy::whenLoaded(
                'discordUser',
                $member,
                fn () => $member->discordUser ? DiscordUserData::fromModel($member->discordUser) : null
            ),
        );
    }
}
