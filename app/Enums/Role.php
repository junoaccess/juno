<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'admin';
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case STAFF = 'staff';
    case CUSTOMER = 'customer';
}
