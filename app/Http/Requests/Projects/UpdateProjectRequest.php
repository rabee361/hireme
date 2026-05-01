<?php

namespace App\Http\Requests\Projects;

use App\Enums\UserType;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user('api');
        $project = $this->route('project');

        return $user?->type === UserType::Customer
            && $project instanceof Project
            && (int) $project->customer_id === (int) $user->id;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'details' => ['sometimes', 'nullable', 'string'],
            'tool1' => ['sometimes', 'required', 'string', 'max:255'],
            'tool2' => ['sometimes', 'required', 'string', 'max:255'],
            'tool3' => ['sometimes', 'required', 'string', 'max:255'],
            'tool4' => ['sometimes', 'required', 'string', 'max:255'],
            'tool5' => ['sometimes', 'required', 'string', 'max:255'],
            'cover_image' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}