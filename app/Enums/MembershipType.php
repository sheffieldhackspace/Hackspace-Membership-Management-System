<?php

namespace App\Enums;

enum MembershipType: string
{
    case Keyholder = 'Keyholder';
    case Member = 'Member';
    case UnpaidMember = 'Unpaid Member';
    case UnpaidKeyholder = 'Unpaid Keyholder';
}
