<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateCompanyProfileRequest;
use App\Models\Company;
use App\Models\CompanyProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const USER_FIELDS = [
        'id',
        'username',
        'email',
        'phone_number',
        'description',
        'cover_image',
        'avatar',
        'is_verified',
    ];

    /**
     * @var array<int, string>
     */
    private const PROFILE_FIELDS = [
        'id',
        'user_id',
        'started_at',
        'employees_count',
        'tech1',
        'tech2',
        'tech3',
    ];

    public function index(Request $request): JsonResponse
    {
        $name = trim((string) $request->query('name', ''));

        $companies = Company::query()
            ->with('profile')
            ->when($name !== '', fn ($query) => $query->where('username', 'like', "%{$name}%"))
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Companies retrieved successfully.',
            'data' => $companies->map(fn (Company $company): array => $this->companyPayload($company))->all(),
        ]);
    }

    public function show(Company $company): JsonResponse
    {
        $company->load('profile');

        return response()->json([
            'message' => 'Company retrieved successfully.',
            'data' => $this->companyPayload($company),
        ]);
    }

    public function update(UpdateCompanyProfileRequest $request, Company $company): JsonResponse
    {
        $user = $request->user('api');

        // Ensure the authenticated user is updating their own profile
        abort_if(! $user || (int) $company->id !== (int) $user->id, 403, 'This action is unauthorized.');

        $validated = $request->validated();

        // Separate user fields from profile fields
        $userFields = array_intersect_key($validated, array_flip([
            'username', 'phone_number', 'description', 'cover_image', 'avatar',
        ]));

        $profileFields = array_intersect_key($validated, array_flip([
            'started_at', 'employees_count', 'tech1', 'tech2', 'tech3',
        ]));

        DB::transaction(function () use ($company, $userFields, $profileFields): void {
            if (! empty($userFields)) {
                $company->update($userFields);
            }

            if (! empty($profileFields)) {
                $company->profile()->updateOrCreate(
                    ['user_id' => $company->id],
                    $profileFields
                );
            }
        });

        $company->refresh()->load('profile');

        return response()->json([
            'message' => 'Company profile updated successfully.',
            'data' => $this->companyPayload($company),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function companyPayload(Company $company): array
    {
        $payload = $company->only(self::USER_FIELDS);
        $payload['type'] = $company->type?->value;
        $payload['created_at'] = $company->created_at?->toISOString();
        $payload['updated_at'] = $company->updated_at?->toISOString();
        $payload['profile'] = $this->profilePayload($company->profile);

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function profilePayload(?CompanyProfile $profile): ?array
    {
        if (! $profile) {
            return null;
        }

        $payload = $profile->only(self::PROFILE_FIELDS);
        $payload['started_at'] = $profile->started_at?->toDateString();
        $payload['created_at'] = $profile->created_at?->toISOString();
        $payload['updated_at'] = $profile->updated_at?->toISOString();

        return $payload;
    }
}