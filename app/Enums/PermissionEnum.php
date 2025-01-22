<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case PROFILEEDIT = 'profile-edit';

    case VIEWUSERS = 'view-users';
    case VIEWUSER = 'view-user';
    case EDITUSERS = 'edit-users';
    case EDITUSER = 'edit-user';

    case VIEWMEMBERS = 'view-members';
    case VIEWOWNMEMBER = 'view-member';
    case EDITMEMBERS = 'edit-members';
    case EDITOWNMEMBER = 'edit-member';
    case CREATEMEMBER = 'create-member';

    case VIEWPWMEMBERREPORT = 'view-pw-member-report';

    public function label(): string
    {
        return match ($this) {
            self::PROFILEEDIT => 'Profile Edit',
            self::VIEWUSERS => 'View Users',
            self::VIEWUSER => 'View User',
            self::EDITUSERS => 'Edit Users',
            self::EDITUSER => 'Edit User',
            self::VIEWMEMBERS => 'View Members',
            self::VIEWOWNMEMBER => 'View Member',
            self::EDITMEMBERS => 'Edit Members',
            self::EDITOWNMEMBER => 'Edit Member',
            self::CREATEMEMBER => 'Create Member',
            self::VIEWPWMEMBERREPORT => 'View PW User Member Report',
        };
    }
}
