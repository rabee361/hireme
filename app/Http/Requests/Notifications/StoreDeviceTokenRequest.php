<?php

namespace App\Http\Requests\Notifications;

use App\Concerns\DeviceTokenValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceTokenRequest extends FormRequest
{
    use DeviceTokenValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->requiredDeviceTokenRules();
    }
}