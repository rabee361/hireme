<?php

namespace App\Enums;

enum NotificationType: string
{
    case AdminAnnouncement = 'admin_announcement';
    case AdApplicationSubmitted = 'ad_application_submitted';
    case ProjectApplicationSubmitted = 'project_application_submitted';
    case AccountVerified = 'account_verified';
}