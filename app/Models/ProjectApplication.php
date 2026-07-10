<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectApplication extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_profile_id',
        'github_link',
        'expected_salary',
        'resume',
        'project_id',
        'status',
        'client_approval_date',
        'delivery_deadline_date',
        'trial_ends_at_date',
        'submission_link',
    ];

    protected function casts(): array
    {
        return [
            'expected_salary' => 'integer',
            'client_approval_date' => 'datetime',
            'delivery_deadline_date' => 'datetime',
            'trial_ends_at_date' => 'datetime',
        ];
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function objections(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Objection::class);
    }
}