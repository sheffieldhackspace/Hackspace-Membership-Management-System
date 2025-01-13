<?php

namespace App\Data;

use App\Models\Member;
use Spatie\LaravelData\Data;

class MemberData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $knownAs,
        public string $membershipType,
        public bool $hasActiveMembership,
        public ?string $joiningDate
    ) {
    }

    public static function fromModel(Member $member): self
    {
        return new self(
            id: $member->id,
            name: $member->name,
            knownAs: $member->known_as,
            membershipType: $member->getMembershipType()->value,
            hasActiveMembership: $member->getHasActiveMembership(),
            joiningDate: $member->getJoiningDate() ? $member->getJoiningDate()->toDateString() : null,
        );
    }

}
