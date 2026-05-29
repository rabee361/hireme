<?php

namespace App\Services\Notifications;

use App\Models\User;
use Illuminate\Support\Collection;

class NotificationAudienceResolver
{
    /**
     * @return Collection<int, User>
     */
    public function resolveUsers(NotificationAudience $audience): Collection
    {
        return User::query()
            ->when($audience->userIds !== [], fn ($query) => $query->whereIn('id', $audience->userIds))
            ->when($audience->userType, fn ($query, $userType) => $query->where('type', $userType->value))
            ->get();
    }
}