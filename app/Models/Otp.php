<?php

namespace App\Models;

use App\Enums\OtpType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Otp extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'code',
        'expires_at',
        'is_used',
    ];

    /**
     * The attributes that should not be updated.
     */
    public const UPDATED_AT = null;

    /**
     * Bootstrap the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $otp): void {
            $otp->code ??= Str::upper(Str::random(6));
            $otp->expires_at ??= now()->addMinutes(15);
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => OtpType::class,
            'created_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_used' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}