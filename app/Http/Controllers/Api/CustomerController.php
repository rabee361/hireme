<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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