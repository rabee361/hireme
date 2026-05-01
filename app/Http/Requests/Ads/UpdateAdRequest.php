<?php

namespace App\Http\Requests\Ads;

use App\Enums\UserType;
use App\Models\Ad;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user('api');
        $ad = $this->route('ad');

        return $user?->type === UserType::Company
            && $ad instanceof Ad
            && (int) $ad->company_id === (int) $user->id;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'job_name' => ['sometimes', 'required', 'string', 'max:255'],
            'req1' => ['sometimes', 'required', 'string', 'max:255'],
            'req2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'req3' => ['sometimes', 'nullable', 'string', 'max:255'],
            'req4' => ['sometimes', 'nullable', 'string', 'max:255'],
            'req5' => ['sometimes', 'nullable', 'string', 'max:255'],
            'task1' => ['sometimes', 'required', 'string', 'max:255'],
            'task2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'task3' => ['sometimes', 'nullable', 'string', 'max:255'],
            'task4' => ['sometimes', 'nullable', 'string', 'max:255'],
            'task5' => ['sometimes', 'nullable', 'string', 'max:255'],
            'feature1' => ['sometimes', 'required', 'string', 'max:255'],
            'feature2' => ['sometimes', 'nullable', 'string', 'max:255'],
            'feature3' => ['sometimes', 'nullable', 'string', 'max:255'],
            'feature4' => ['sometimes', 'nullable', 'string', 'max:255'],
            'feature5' => ['sometimes', 'nullable', 'string', 'max:255'],
            'additional_details' => ['sometimes', 'nullable', 'string'],
            'github_required' => ['sometimes', 'required', 'boolean'],
            'resume_required' => ['sometimes', 'required', 'boolean'],
            'prev_work_required' => ['sometimes', 'required', 'boolean'],
            'expected_salary_required' => ['sometimes', 'required', 'boolean'],
        ];
    }
}