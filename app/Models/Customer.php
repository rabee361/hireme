<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends TypedUser
{
    protected static function typeValue(): string
    {
        return UserType::Customer->value;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'customer_id');
    }
}