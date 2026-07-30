<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentProfile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'address',
        'hour_cost',
        'experience_years',
        'tech1',
        'tech2',
        'tech3',
        'college',
        'title',
        'git_link',
        'linked_link',
        'bio',
        'university_name',
        'is_graduated',
    ];

    protected function casts(): array
    {
        return [
            'hour_cost' => 'decimal:2',
            'experience_years' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function adApplications(): HasMany
    {
        return $this->hasMany(AdApplication::class);
    }

    public function projectApplications(): HasMany
    {
        return $this->hasMany(ProjectApplication::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(StudentExperience::class);
    }
}