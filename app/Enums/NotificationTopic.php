<?php

namespace App\Enums;

enum NotificationTopic: string
{
    case AllStudents = 'all-students';
    case AllCompanies = 'all-companies';
    case AllCustomers = 'all-customers';
}