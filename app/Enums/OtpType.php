<?php

namespace App\Enums;

enum OtpType: string
{
    case Signup = 'signup';
    case PasswordReset = 'password_reset';
}