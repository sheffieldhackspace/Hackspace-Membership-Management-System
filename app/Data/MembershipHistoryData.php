<?php

namespace App\Data;

use App\Models\MembershipHistory;
use Spatie\LaravelData\Data;

class MembershipHistoryData extends Data
{
    public function __construct(
        public string $id,
        public string $memberId,
        public string $membershipType,
        public string $startDate,
    ) {
    }

    public static function fromModel(MembershipHistory $membershipHistory): self
    {
        return new self(
            id: $membershipHistory->id,
            memberId: $membershipHistory->member_id,
            membershipType: $membershipHistory->membership_type->label(),
            startDate: $membershipHistory->created_at->format('d/m/Y'),
        );
    }
}
