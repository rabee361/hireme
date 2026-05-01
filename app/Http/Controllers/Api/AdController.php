<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ads\StoreAdRequest;
use App\Http\Requests\Ads\UpdateAdRequest;
use App\Models\Ad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const COMPANY_RELATION_COLUMNS = [
        'id',
        'username',
        'description',
        'avatar',
        'cover_image',
    ];

    /**
     * @var array<int, string>
     */
    private const AD_PAYLOAD_FIELDS = [
        'id',
        'job_name',
        'req1',
        'req2',
        'req3',
        'req4',
        'req5',
        'task1',
        'task2',
        'task3',
        'task4',
        'task5',
        'feature1',
        'feature2',
        'feature3',
        'feature4',
        'feature5',
        'additional_details',
        'github_required',
        'resume_required',
        'prev_work_required',
        'expected_salary_required',
        'company_id',
    ];

    public function index(): JsonResponse
    {
        $ads = Ad::query()
            ->with(['company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1))])
            ->withCount('applications')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Ads retrieved successfully.',
            'data' => $ads->map(fn (Ad $ad): array => $this->adPayload($ad))->all(),
        ]);
    }

    public function store(StoreAdRequest $request): JsonResponse
    {
        $ad = Ad::query()->create([
            ...$request->validated(),
            'company_id' => $request->user('api')->id,
        ]);

        $ad->load('company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Ad created successfully.',
            'data' => $this->adPayload($ad),
        ], 201);
    }

    public function show(Ad $ad): JsonResponse
    {
        $ad->load('company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Ad retrieved successfully.',
            'data' => $this->adPayload($ad),
        ]);
    }

    public function update(UpdateAdRequest $request, Ad $ad): JsonResponse
    {
        $ad->fill($request->validated());
        $ad->save();

        $ad->load('company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Ad updated successfully.',
            'data' => $this->adPayload($ad),
        ]);
    }

    public function destroy(Request $request, Ad $ad): JsonResponse
    {
        $user = $request->user('api');

        abort_if(
            ! $user || $user->type !== UserType::Company || (int) $ad->company_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        $ad->delete();

        return response()->json([
            'message' => 'Ad deleted successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function adPayload(Ad $ad): array
    {
        $company = $ad->company;
        $payload = $ad->only(self::AD_PAYLOAD_FIELDS);

        $payload['applicants_count'] = (int) ($ad->applications_count ?? 0);
        $payload['company'] = [
            'id' => $company?->id,
            'name' => $company?->username,
            'description' => $company?->description,
            'image' => $company?->avatar ?? $company?->cover_image,
        ];
        $payload['since'] = $this->since($ad);
        $payload['created_at'] = $ad->created_at?->toISOString();
        $payload['updated_at'] = $ad->updated_at?->toISOString();

        return $payload;
    }

    private function since(Ad $ad): ?string
    {
        if (! $ad->created_at) {
            return null;
        }

        $days = $ad->created_at->diffInDays(now());

        if ($days >= 1) {
            return $days.' '.Str::plural('day', $days).' ago';
        }

        $hours = $ad->created_at->diffInHours(now());

        if ($hours >= 1) {
            return $hours.' '.Str::plural('hour', $hours).' ago';
        }

        return 'less than an hour ago';
    }
}