<?php

namespace App\Enums;

enum NotificationAudienceType: string
{
    case Users = 'users';
    case UserType = 'user_type';
    case Topic = 'topic';
}