<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Company;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SavedStudentController extends Controller
{
    /**
     * Validate the authenticated user is a Company and return the Company model.
     */
    private function getAuthenticatedCompany(Request $request): Company
    {
        $user = $request->user('api');

        abort_if($user === null || $user->type !== UserType::Company, 403, 'This action is unauthorized.');

        /** @var Company $company */
        $company = Company::findOrFail($user->id);

        return $company;
    }

    public function index(Request $request): JsonResponse
    {
        $company = $this->getAuthenticatedCompany($request);

        $students = $company->savedStudents()
            ->with('profile')
            ->latest('company_saved_students.created_at')
            ->get();

        return response()->json([
            'message' => 'Saved students retrieved successfully.',
            'data' => StudentResource::collection($students),
        ]);
    }

    public function store(Request $request, Student $student): JsonResponse
    {
        $company = $this->getAuthenticatedCompany($request);

        $attached = $company->savedStudents()->syncWithoutDetaching([$student->id]);

        $wasAlreadySaved = empty($attached['attached']);

        return response()->json([
            'message' => $wasAlreadySaved ? 'Student is already in your saved list.' : 'Student saved successfully.',
        ]);
    }

    public function destroy(Request $request, Student $student): JsonResponse
    {
        $company = $this->getAuthenticatedCompany($request);

        $detached = $company->savedStudents()->detach($student->id);

        return response()->json([
            'message' => $detached > 0
                ? 'Student removed from saved list successfully.'
                : 'Student was not in your saved list.',
        ]);
    }
}
