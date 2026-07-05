<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applications\StoreAdApplicationRequest;
use App\Http\Requests\Applications\UpdateAdApplicationRequest;
use App\Http\Resources\AdApplicationResource;
use App\Models\Ad;
use App\Models\AdApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdApplicationController extends Controller
{
    /**
     * List applications for a specific ad (Company only).
     */
    public function index(Request $request, Ad $ad): JsonResponse
    {
        $user = $request->user('api');

        abort_if(
            ! $user || $user->type !== UserType::Company || (int) $ad->company_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        $applications = $ad->applications()
            ->with('studentProfile.user')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Ad applications retrieved successfully.',
            'data' => AdApplicationResource::collection($applications),
        ]);
    }

    /**
     * List authenticated student's ad applications.
     */
    public function myApplications(Request $request): JsonResponse
    {
        $user = $request->user('api');

        abort_if(! $user || $user->type !== UserType::Student, 403, 'This action is unauthorized.');

        $profile = $user->studentProfile;

        abort_if(! $profile, 403, 'Student profile not found.');

        $applications = $profile->adApplications()
            ->with([
                'ad.company:id,username,description,avatar,cover_image',
                'ad' => fn ($q) => $q->withCount('applications'),
                'studentProfile.user',
            ])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'My ad applications retrieved successfully.',
            'data' => AdApplicationResource::collection($applications),
        ]);
    }

    /**
     * Apply to an ad (Student only).
     */
    public function store(StoreAdApplicationRequest $request): JsonResponse
    {
        $user = $request->user('api');

        $profile = $user->studentProfile;

        abort_if(! $profile, 403, 'Student profile not found.');

        $validated = $request->validated();

        $adId = (int) $validated['ad_id'];

        // Check the student hasn't already applied
        $existing = $profile->adApplications()->where('ad_id', $adId)->exists();

        abort_if($existing, 409, 'You have already applied to this ad.');

        $application = $profile->adApplications()->create([
            'ad_id' => $adId,
            'github_link' => $validated['github_link'],
            'expected_salary' => $validated['expected_salary'] ?? null,
            'resume' => $validated['resume'],
        ]);

        $application->load([
            'studentProfile.user',
            'ad.company:id,username,description,avatar,cover_image',
            'ad' => fn ($q) => $q->withCount('applications'),
        ]);

        return response()->json([
            'message' => 'Ad application submitted successfully.',
            'data' => new AdApplicationResource($application),
        ], 201);
    }

    /**
     * Update application details (Student only - own application).
     */
    public function update(UpdateAdApplicationRequest $request, AdApplication $adApplication): JsonResponse
    {
        $user = $request->user('api');

        // Ensure the student owns this application
        $profile = $user->studentProfile;

        abort_if(
            ! $profile || (int) $adApplication->student_profile_id !== (int) $profile->id,
            403,
            'This action is unauthorized.'
        );

        $adApplication->update($request->validated());

        $adApplication->load([
            'studentProfile.user',
            'ad.company:id,username,description,avatar,cover_image',
            'ad' => fn ($q) => $q->withCount('applications'),
        ]);

        return response()->json([
            'message' => 'Ad application updated successfully.',
            'data' => new AdApplicationResource($adApplication),
        ]);
    }

    /**
     * Delete application (Student only - own application).
     */
    public function destroy(Request $request, AdApplication $adApplication): JsonResponse
    {
        $user = $request->user('api');

        // Ensure the student owns this application
        $profile = $user->studentProfile;

        abort_if(
            ! $profile || (int) $adApplication->student_profile_id !== (int) $profile->id,
            403,
            'This action is unauthorized.'
        );

        $adApplication->delete();

        return response()->json([
            'message' => 'Ad application deleted successfully.',
        ]);
    }
}
