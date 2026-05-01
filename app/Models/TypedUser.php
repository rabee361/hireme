<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

abstract class TypedUser extends User
{
    protected $table = 'users';

    abstract protected static function typeValue(): string;

    protected static function booted(): void
    {
        static::addGlobalScope('type', fn (Builder $builder) => $builder->where('type', static::typeValue()));

        static::creating(function (self $user): void {
            $user->type = static::typeValue();
        });
    }
}