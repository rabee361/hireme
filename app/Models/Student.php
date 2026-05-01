<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends TypedUser
{
    protected static function typeValue(): string
    {
        return UserType::Student->value;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }
}