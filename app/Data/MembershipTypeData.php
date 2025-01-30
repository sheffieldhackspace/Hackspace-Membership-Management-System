<?php

namespace App\Data;

use App\Enums\MembershipType;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class MembershipTypeData extends Data
{
    public function __construct(
        public string $value,
        public string $label,
    ) {}

    public static function fromEnum(MembershipType $membershipType): self
    {
        return new self(
            value: $membershipType->value,
            label: $membershipType->label(),
        );
    }

    public static function getAll(): Collection
    {
        return collect(MembershipType::cases())
            ->map(fn (MembershipType $membershipType) => self::fromEnum($membershipType));
    }
}
