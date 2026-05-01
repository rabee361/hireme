<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ad extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'job_name',
        'req1',
        'req2',
        'req3',
        'req4',
        'req5',
        'task1',
        'task2',
        'task3',
        'task4',
        'task5',
        'feature1',
        'feature2',
        'feature3',
        'feature4',
        'feature5',
        'additional_details',
        'github_required',
        'resume_required',
        'prev_work_required',
        'expected_salary_required',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'github_required' => 'boolean',
            'resume_required' => 'boolean',
            'prev_work_required' => 'boolean',
            'expected_salary_required' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(AdApplication::class);
    }
}