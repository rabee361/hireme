<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'details',
        'additional_details',
        'tool1',
        'tool2',
        'tool3',
        'tool4',
        'tool5',
        'name',
        'cover_image',
        'customer_id',
        'status',
        'delivery_days',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(ProjectApplication::class);
    }
}