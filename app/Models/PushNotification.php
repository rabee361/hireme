<?php

namespace App\Models;

use App\Enums\NotificationAudienceType;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PushNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'body',
        'data',
        'audience_type',
        'audience_value',
        'initiator_id',
        'queued_at',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => NotificationType::class,
            'data' => 'array',
            'audience_type' => NotificationAudienceType::class,
            'queued_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(PushNotificationRecipient::class);
    }
}