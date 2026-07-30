<?php

namespace App\Http\Requests\Projects;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('api')?->type === UserType::Customer;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'additional_details' => ['nullable', 'string'],
            'tool1' => ['required', 'string', 'max:255'],
            'tool2' => ['nullable', 'string', 'max:255'],
            'tool3' => ['nullable', 'string', 'max:255'],
            'tool4' => ['nullable', 'string', 'max:255'],
            'tool5' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['required', 'string', 'max:255'],
        ];
    }
}