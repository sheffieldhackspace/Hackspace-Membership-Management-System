<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case VIEWUSERS = 'view-users';
    case VIEWOWNUSER = 'view-user';
    case EDITUSERS = 'edit-users';
    case EDITOWNUSER = 'edit-user';

    case VIEWMEMBERS = 'view-members';
    case VIEWOWNMEMBER = 'view-member';
    case EDITMEMBERS = 'edit-members';
    case EDITOWNMEMBER = 'edit-member';
    case CREATEMEMBER = 'create-member';
    case CHANGEMEMBERSHIPTYPE = 'change-membership-type';

    case VIEWPWMEMBERREPORT = 'view-pw-member-report';

    public function label(): string
    {
        return match ($this) {
            self::VIEWUSERS => 'View Users',
            self::VIEWOWNUSER => 'View User',
            self::EDITUSERS => 'Edit Users',
            self::EDITOWNUSER => 'Edit User',
            self::VIEWMEMBERS => 'View Members',
            self::VIEWOWNMEMBER => 'View Member',
            self::EDITMEMBERS => 'Edit Members',
            self::EDITOWNMEMBER => 'Edit Member',
            self::CREATEMEMBER => 'Create Member',
            self::VIEWPWMEMBERREPORT => 'View PW User Member Report',
        };
    }
}
