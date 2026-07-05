<?php

namespace App\Http\Requests\Applications;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectApplicationRequest extends FormRequest
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
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'github_link' => ['required', 'string', 'max:2048'],
            'expected_salary' => ['nullable', 'integer', 'min:0'],
            'resume' => ['required', 'string', 'max:2048'],
        ];
    }
}
