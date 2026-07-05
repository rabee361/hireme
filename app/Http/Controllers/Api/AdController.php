<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ads\StoreAdRequest;
use App\Http\Requests\Ads\UpdateAdRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function index(): JsonResponse
    {
        $ads = Ad::query()
            ->with(['company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1))])
            ->withCount('applications')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Ads retrieved successfully.',
            'data' => AdResource::collection($ads),
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
            'data' => new AdResource($ad),
        ], 201);
    }

    public function show(Ad $ad): JsonResponse
    {
        $ad->load('company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Ad retrieved successfully.',
            'data' => new AdResource($ad),
        ]);
    }

    public function update(UpdateAdRequest $request, Ad $ad): JsonResponse
    {
        $user = $request->user('api');

        abort_if(
            ! $user || $user->type !== UserType::Company || (int) $ad->company_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        $ad->fill($request->validated());
        $ad->save();

        $ad->load('company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Ad updated successfully.',
            'data' => new AdResource($ad),
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

    public function myAds(Request $request): JsonResponse
    {
        $user = $request->user('api');

        abort_if(! $user || $user->type !== UserType::Company, 403, 'This action is unauthorized.');

        $ads = Ad::query()
            ->where('company_id', $user->id)
            ->with(['company:id,'.implode(',', array_slice(self::COMPANY_RELATION_COLUMNS, 1))])
            ->withCount('applications')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'My ads retrieved successfully.',
            'data' => AdResource::collection($ads),
        ]);
    }
}
