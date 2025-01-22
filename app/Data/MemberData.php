<?php

namespace App\Data;

use App\Models\Member;
use App\Models\MembershipHistory;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class MemberData extends Data
{
    /**
     * @param string $id
     * @param string $name
     * @param string $knownAs
     * @param string $membershipType
     * @param bool $hasActiveMembership
     * @param string|null $joiningDate
     * @param EmailAddressData[]|Lazy|Null $emailAddresses
     * @param PostalAddressData|Lazy|Null $postalAddress
     * @param TrusteeHistoryData[]|Lazy|Null $trusteeHistories
     * @param MembershipHistoryData[]|Lazy|Null $membershipHistories
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $knownAs,
        public string $membershipType,
        public bool $hasActiveMembership,
        public ?string $joiningDate,
        public array|Lazy $emailAddresses,
        public PostalAddressData|Lazy $postalAddress,
        public array|Lazy $trusteeHistory,
        public array|Lazy $membershipHistory,
    ) {
    }

    public static function fromModel(Member $member): self
    {
        return new self(
            id: $member->id,
            name: $member->name,
            knownAs: $member->known_as,
            membershipType: $member->getMembershipType()->label(),
            hasActiveMembership: $member->getHasActiveMembership(),
            joiningDate: $member->getJoiningDate() ? $member->getJoiningDate()->toDateString() : null,
            emailAddresses: Lazy::whenLoaded(
                'emailAddresses',
                $member,
                fn () => $member->emailAddresses->map(fn ($emailAddress) => EmailAddressData::fromModel($emailAddress))
            ),
            postalAddress: Lazy::whenLoaded(
                'postalAddresses',
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
        );
    }

}
