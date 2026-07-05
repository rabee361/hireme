<?php

namespace App\Http\Requests\Applications;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('api')?->type === UserType::Student;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'github_link' => ['sometimes', 'required', 'string', 'max:2048'],
            'expected_salary' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'resume' => ['sometimes', 'required', 'string', 'max:2048'],
        ];
    }
}
