<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;

class SendDashboardNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'string', 'in:admin_announcement'],
            'audience_type' => ['required', 'string', 'in:users,user_type,topic'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'user_type' => ['nullable', 'string', 'in:student,customer,company,admin'],
            'topic' => ['nullable', 'string', 'in:all-students,all-companies,all-customers'],
            'data' => ['nullable', 'array'],
        ];
    }
}