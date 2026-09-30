<?php

namespace App\Enums;

enum UserRole: string
{
    case Volunteer = 'volunteer';
    case Eo = 'eo';
    case Admin = 'admin';
}
