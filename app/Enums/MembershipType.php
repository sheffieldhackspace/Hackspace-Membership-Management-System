<?php

namespace App\Enums;

enum MembershipType
{
    case Keyholder;
    case Member;
    case UnpaidMember;
    case UnpaidKeyholder;
}
