<?php

namespace App\Http\Requests\Profile;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyProfileRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user('api');

        return $user !== null && $user->type === UserType::Company;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            // users table fields
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($this->user('api')->id)],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'description' => ['sometimes', 'nullable', 'string'],
            'cover_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'avatar' => ['sometimes', 'nullable', 'string', 'max:2048'],

            // company_profiles table fields
            'started_at' => ['sometimes', 'nullable', 'date'],
            'employees_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'tech1' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tech2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tech3' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
