<?php

namespace App\Http\Requests\Ads;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('api')?->type === UserType::Company;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'job_name' => ['required', 'string', 'max:255'],
            'req1' => ['required', 'string', 'max:255'],
            'req2' => ['nullable', 'string', 'max:255'],
            'req3' => ['nullable', 'string', 'max:255'],
            'req4' => ['nullable', 'string', 'max:255'],
            'req5' => ['nullable', 'string', 'max:255'],
            'task1' => ['required', 'string', 'max:255'],
            'task2' => ['nullable', 'string', 'max:255'],
            'task3' => ['nullable', 'string', 'max:255'],
            'task4' => ['nullable', 'string', 'max:255'],
            'task5' => ['nullable', 'string', 'max:255'],
            'feature1' => ['required', 'string', 'max:255'],
            'feature2' => ['nullable', 'string', 'max:255'],
            'feature3' => ['nullable', 'string', 'max:255'],
            'feature4' => ['nullable', 'string', 'max:255'],
            'feature5' => ['nullable', 'string', 'max:255'],
            'additional_details' => ['nullable', 'string'],
            'github_required' => ['required', 'boolean'],
            'resume_required' => ['required', 'boolean'],
            'prev_work_required' => ['required', 'boolean'],
            'expected_salary_required' => ['required', 'boolean'],
        ];
    }
}