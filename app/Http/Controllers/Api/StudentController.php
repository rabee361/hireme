<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateStudentProfileRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $name = trim((string) $request->query('name', ''));

        $students = Student::query()
            ->with('profile')
            ->when($name !== '', fn ($query) => $query->where('username', 'like', "%{$name}%"))
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Students retrieved successfully.',
            'data' => StudentResource::collection($students),
        ]);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load('profile');

        return response()->json([
            'message' => 'Student retrieved successfully.',
            'data' => new StudentResource($student),
        ]);
    }

    public function update(UpdateStudentProfileRequest $request, Student $student): JsonResponse
    {
        $user = $request->user('api');

        // Ensure the authenticated user is updating their own profile
        abort_if(! $user || (int) $student->id !== (int) $user->id, 403, 'This action is unauthorized.');

        $validated = $request->validated();

        // Separate user fields from profile fields
        $userFields = array_intersect_key($validated, array_flip([
            'username', 'phone_number', 'description', 'cover_image', 'avatar',
        ]));

        $profileFields = array_intersect_key($validated, array_flip([
            'address', 'hour_cost', 'experience_years', 'tech1', 'tech2', 'tech3',
            'college', 'title',
        ]));

        DB::transaction(function () use ($student, $userFields, $profileFields): void {
            if (! empty($userFields)) {
                $student->update($userFields);
            }

            if (! empty($profileFields)) {
                $student->profile()->updateOrCreate(
                    ['user_id' => $student->id],
                    $profileFields
                );
            }
        });

        $student->refresh()->load('profile');

        return response()->json([
            'message' => 'Student profile updated successfully.',
            'data' => new StudentResource($student),
        ]);
    }
}
