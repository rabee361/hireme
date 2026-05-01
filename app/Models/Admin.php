<?php

namespace App\Models;

use App\Enums\UserType;

class Admin extends TypedUser
{
    protected static function typeValue(): string
    {
        return UserType::Admin->value;
    }
}