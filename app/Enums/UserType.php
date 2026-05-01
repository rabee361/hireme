<?php

namespace App\Enums;

enum UserType: string
{
    case Student = 'student';
    case Customer = 'customer';
    case Admin = 'admin';
    case Company = 'company';
}