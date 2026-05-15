<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
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

        $students = Student::query()
            ->with('profile')
            ->when($name !== '', fn ($query) => $query->where('username', 'like', "%{$name}%"))
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Students retrieved successfully.',
            'data' => $students->map(fn (Student $student): array => $this->studentPayload($student))->all(),
        ]);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load('profile');

        return response()->json([
            'message' => 'Student retrieved successfully.',
            'data' => $this->studentPayload($student),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function studentPayload(Student $student): array
    {
        $payload = $student->only(self::USER_FIELDS);
        $payload['type'] = $student->type?->value;
        $payload['created_at'] = $student->created_at?->toISOString();
        $payload['updated_at'] = $student->updated_at?->toISOString();
        $payload['profile'] = $this->profilePayload($student->profile);

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function profilePayload(?StudentProfile $profile): ?array
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