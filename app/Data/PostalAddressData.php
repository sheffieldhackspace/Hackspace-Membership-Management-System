<?php

namespace App\Data;

use App\Models\PostalAddress;
use Spatie\LaravelData\Data;

class PostalAddressData extends Data
{
    public function __construct(
        public string $id,
        public string $memberId,
        public string $line1,
        public ?string $line2,
        public ?string $line3,
        public string $city,
        public ?string $county,
        public string $postcode
    ) {
    }

    public static function fromModel(PostalAddress $postalAddress): self
    {
        return new self(
            id: $postalAddress->id,
            memberId: $postalAddress->member_id,
            line1: $postalAddress->line_1,
            line2: $postalAddress->line_2,
            line3: $postalAddress->line_3,
            city: $postalAddress->city,
            county: $postalAddress->county,
            postcode: $postalAddress->postcode
        );
    }
}
