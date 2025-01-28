<?php

namespace App\Data;

use App\Models\User;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $emailAddress,
        public ?string $emailVerifiedAt,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            emailAddress: $user->email,
            emailVerifiedAt: $user->email_verified_at ? $user->email_verified_at->toDateString() : null,
        );
    }
}
