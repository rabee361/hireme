<?php

namespace App\Http\Requests\Auth;

use App\Concerns\DeviceTokenValidationRules;
use App\Enums\OtpType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
{
    use DeviceTokenValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'type' => ['required', 'string', Rule::in([
                OtpType::Signup->value,
                OtpType::PasswordReset->value,
            ])],
            ...$this->optionalDeviceTokenRules(),
        ];
    }
}