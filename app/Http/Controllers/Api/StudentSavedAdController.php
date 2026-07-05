<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentSavedAdController extends Controller
{
    private function getAuthenticatedStudent(Request $request): Student
    {
        $user = $request->user('api');

        abort_if($user === null || $user->type !== UserType::Student, 403, 'This action is unauthorized.');

        /** @var Student $student */
        $student = Student::findOrFail($user->id);

        return $student;
    }

    public function index(Request $request): JsonResponse
    {
        $student = $this->getAuthenticatedStudent($request);

        $ads = $student->savedAds()
            ->with(['company:id,username,description,avatar,cover_image'])
            ->withCount('applications')
            ->latest('student_saved_ads.created_at')
            ->get();

        return response()->json([
            'message' => 'Saved ads retrieved successfully.',
            'data' => AdResource::collection($ads),
        ]);
    }

    public function store(Request $request, Ad $ad): JsonResponse
    {
        $student = $this->getAuthenticatedStudent($request);

        $attached = $student->savedAds()->syncWithoutDetaching([$ad->id]);

        $wasAlreadySaved = empty($attached['attached']);

        return response()->json([
            'message' => $wasAlreadySaved ? 'Ad is already in your saved list.' : 'Ad saved successfully.',
        ]);
    }

    public function destroy(Request $request, Ad $ad): JsonResponse
    {
        $student = $this->getAuthenticatedStudent($request);

        $detached = $student->savedAds()->detach($ad->id);

        return response()->json([
            'message' => $detached > 0
                ? 'Ad removed from saved list successfully.'
                : 'Ad was not in your saved list.',
        ]);
    }
}
