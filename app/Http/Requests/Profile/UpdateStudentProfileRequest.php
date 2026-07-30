<?php

namespace App\Http\Requests\Profile;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentProfileRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        $user = $this->user('api');

        return $user !== null && $user->type === UserType::Student;
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

            // student_profiles table fields
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'hour_cost' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'experience_years' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'tech1' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tech2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tech3' => ['sometimes', 'nullable', 'string', 'max:255'],
            'college' => ['sometimes', 'nullable', 'string', 'max:255'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'git_link' => ['sometimes', 'nullable', 'string', 'max:255'],
            'linked_link' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bio' => ['sometimes', 'nullable', 'string'],
            'university_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_graduated' => ['sometimes', 'nullable', 'boolean'],

            // experiences
            'experiences' => ['sometimes', 'nullable', 'array'],
            'experiences.*.exp_name' => ['required_with:experiences', 'string', 'max:255'],
            'experiences.*.is_freelancer' => ['required_with:experiences', 'boolean'],
            'experiences.*.job_title' => ['required_with:experiences', 'string', 'max:255'],
            'experiences.*.start_date' => ['required_with:experiences', 'string', 'max:50'],
            'experiences.*.end_date' => ['nullable', 'string', 'max:50'],
            'experiences.*.until_now' => ['required_with:experiences', 'boolean'],
        ];
    }
}
