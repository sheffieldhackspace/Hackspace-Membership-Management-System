<?php

namespace App\Data;

use App\Models\EmailAddress;
use Spatie\LaravelData\Data;

class EmailAddressData extends Data
{
    public function __construct(
        public string|null $id,
        public string|null $memberId,
        public string $emailAddress,
        public bool $isPrimary,
        public string|null $verifiedAt
    ) {
    }

    public static function fromModel(EmailAddress $emailAddress): self
    {
        return new self(
            id: $emailAddress->id,
            memberId: $emailAddress->member_id,
            emailAddress: $emailAddress->email_address,
            isPrimary: $emailAddress->is_primary,
            verifiedAt: $emailAddress->verified_at ? $emailAddress->verified_at->toDateTimeString() : null
        );
    }
}
