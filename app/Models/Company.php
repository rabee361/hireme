<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends TypedUser
{
    protected static function typeValue(): string
    {
        return UserType::Company->value;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class, 'company_id');
    }
}