<?php

namespace App\Data;

use App\Models\TrusteeHistory;
use Spatie\LaravelData\Data;

class TrusteeHistoryData extends Data
{
    public function __construct(
        public string $id,
        public string $memberId,
        public ?string $electedAt,
        public ?string $resignedAt
    ) {}

    public static function fromModel(TrusteeHistory $trusteeHistory): self
    {
        return new self(
            id: $trusteeHistory->id,
            memberId: $trusteeHistory->member_id,
            electedAt: $trusteeHistory->elected_at ? $trusteeHistory->elected_at->format('d/m/Y') : null,
            resignedAt: $trusteeHistory->resigned_at ? $trusteeHistory->resigned_at->format('d/m/Y') : null
        );
    }
}
