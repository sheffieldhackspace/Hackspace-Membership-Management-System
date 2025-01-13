<?php

namespace App\Enums;

enum MembershipType: string
{
    case KEYHOLDER = 'keyholder';
    case MEMBER = 'member';
    case UNPAIDMEMBER = 'unpaid-member';
    case UNPAIDKEYHOLDER = 'unpaid-keyholder';

    public function label(): string
    {
        return match ($this) {
            MembershipType::MEMBER => 'Member',
            MembershipType::KEYHOLDER => 'Keyholder',
            MembershipType::UNPAIDMEMBER => 'Unpaid Member',
            MembershipType::UNPAIDKEYHOLDER => 'Unpaid Keyholder',
        };
    }
}
