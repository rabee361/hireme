<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateCustomerProfileRequest;
use App\Models\Customer;
use App\Models\CustomerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
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
        'address',
        'hour_cost',
        'experience_years',
        'tech1',
        'tech2',
        'tech3',
        'college',
        'title',
        'git_link',
        'linked_link',
        'bio',
        'university_name',
        'is_graduated',
    ];

    public function index(Request $request): JsonResponse
    {
        $name = trim((string) $request->query('name', ''));

        $customers = Customer::query()
            ->with('profile')
            ->when($name !== '', fn ($query) => $query->where('username', 'like', "%{$name}%"))
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Customers retrieved successfully.',
            'data' => $customers->map(fn (Customer $customer): array => $this->customerPayload($customer))->all(),
        ]);
    }

    public function show(Customer $customer): JsonResponse
    {
        $customer->load('profile');

        return response()->json([
            'message' => 'Customer retrieved successfully.',
            'data' => $this->customerPayload($customer),
        ]);
    }

    public function update(UpdateCustomerProfileRequest $request, Customer $customer): JsonResponse
    {
        $user = $request->user('api');

        // Ensure the authenticated user is updating their own profile
        abort_if(! $user || (int) $customer->id !== (int) $user->id, 403, 'This action is unauthorized.');

        $validated = $request->validated();

        // Separate user fields from profile fields
        $userFields = array_intersect_key($validated, array_flip([
            'username', 'phone_number', 'description', 'cover_image', 'avatar',
        ]));

        $profileFields = array_intersect_key($validated, array_flip([
            'address', 'hour_cost', 'experience_years', 'tech1', 'tech2', 'tech3',
            'college', 'title', 'git_link', 'linked_link', 'bio', 'university_name', 'is_graduated'
        ]));

        DB::transaction(function () use ($customer, $userFields, $profileFields): void {
            if (! empty($userFields)) {
                $customer->update($userFields);
            }

            if (! empty($profileFields)) {
                $customer->profile()->updateOrCreate(
                    ['user_id' => $customer->id],
                    $profileFields
                );
            }
        });

        $customer->refresh()->load('profile');

        return response()->json([
            'message' => 'Customer profile updated successfully.',
            'data' => $this->customerPayload($customer),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function customerPayload(Customer $customer): array
    {
        $payload = $customer->only(self::USER_FIELDS);
        $payload['type'] = $customer->type?->value;
        $payload['created_at'] = $customer->created_at?->toISOString();
        $payload['updated_at'] = $customer->updated_at?->toISOString();
        $payload['profile'] = $this->profilePayload($customer->profile);

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function profilePayload(?CustomerProfile $profile): ?array
    {
        if (! $profile) {
            return null;
        }

        $payload = $profile->only(self::PROFILE_FIELDS);
        $payload['created_at'] = $profile->created_at?->toISOString();
        $payload['updated_at'] = $profile->updated_at?->toISOString();

        return $payload;
    }
}