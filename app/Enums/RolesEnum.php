<?php

namespace App\Enums;

enum RolesEnum: string
{

    case MEMBER = 'member';
    case KEYHOLDER = 'keyholder';
    case TOOLTRAINER = 'tool-trainer';
    case ADMIN = 'admin';
    case PWUSER = 'pw-user';

    public function label(): string
    {
        return match ($this) {
            RolesEnum::MEMBER => 'Member',
            RolesEnum::KEYHOLDER => 'Keyholder',
            RolesEnum::TOOLTRAINER => 'Tool Trainer',
            RolesEnum::ADMIN => 'Admin',
            RolesEnum::PWUSER => 'Portland Works User',
        };
    }
}
