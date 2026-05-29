<?php

namespace App\Http\Requests\Auth;

use App\Concerns\DeviceTokenValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'password' => ['required', 'string'],
            ...$this->optionalDeviceTokenRules(),
        ];
    }
}