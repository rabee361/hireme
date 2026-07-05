<?php

namespace App\Models;

use App\Enums\UserType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends TypedUser
{
    protected static function typeValue(): string
    {
        return UserType::Student->value;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'user_id');
    }

    public function savedByCompanies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_saved_students', 'student_id', 'company_id')
            ->withTimestamps();
    }

    public function savedAds(): BelongsToMany
    {
        return $this->belongsToMany(Ad::class, 'student_saved_ads', 'student_id', 'ad_id')
            ->withTimestamps();
    }
}