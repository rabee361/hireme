<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Applications\StoreProjectApplicationRequest;
use App\Http\Requests\Applications\UpdateProjectApplicationRequest;
use App\Http\Resources\ProjectApplicationResource;
use App\Models\Project;
use App\Models\ProjectApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectApplicationController extends Controller
{
    /**
     * List applications for a specific project (Customer only).
     */
    public function index(Request $request, Project $project): JsonResponse
    {
        $user = $request->user('api');

        abort_if(
            ! $user || $user->type !== UserType::Customer || (int) $project->customer_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        $applications = $project->applications()
            ->with('studentProfile.user')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Project applications retrieved successfully.',
            'data' => ProjectApplicationResource::collection($applications),
        ]);
    }

    /**
     * List authenticated student's project applications.
     */
    public function myApplications(Request $request): JsonResponse
    {
        $user = $request->user('api');

        abort_if(! $user || $user->type !== UserType::Student, 403, 'This action is unauthorized.');

        $profile = $user->studentProfile;

        abort_if(! $profile, 403, 'Student profile not found.');

        $applications = $profile->projectApplications()
            ->with([
                'project.customer:id,username,description,avatar,cover_image',
                'project' => fn ($q) => $q->withCount('applications'),
                'studentProfile.user',
            ])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'My project applications retrieved successfully.',
            'data' => ProjectApplicationResource::collection($applications),
        ]);
    }

    /**
     * Apply to a project (Student only).
     */
    public function store(StoreProjectApplicationRequest $request): JsonResponse
    {
        $user = $request->user('api');

        $profile = $user->studentProfile;

        abort_if(! $profile, 403, 'Student profile not found.');

        $validated = $request->validated();

        $projectId = (int) $validated['project_id'];

        // Check the student hasn't already applied
        $existing = $profile->projectApplications()->where('project_id', $projectId)->exists();

        abort_if($existing, 409, 'You have already applied to this project.');

        $application = $profile->projectApplications()->create([
            'project_id' => $projectId,
            'github_link' => $validated['github_link'],
            'expected_salary' => $validated['expected_salary'] ?? null,
            'resume' => $validated['resume'],
        ]);

        $application->load([
            'studentProfile.user',
            'project.customer:id,username,description,avatar,cover_image',
            'project' => fn ($q) => $q->withCount('applications'),
        ]);

        return response()->json([
            'message' => 'Project application submitted successfully.',
            'data' => new ProjectApplicationResource($application),
        ], 201);
    }

    /**
     * Update application details (Student only - own application).
     */
    public function update(UpdateProjectApplicationRequest $request, ProjectApplication $projectApplication): JsonResponse
    {
        $user = $request->user('api');

        // Ensure the student owns this application
        $profile = $user->studentProfile;

        abort_if(
            ! $profile || (int) $projectApplication->student_profile_id !== (int) $profile->id,
            403,
            'This action is unauthorized.'
        );

        $projectApplication->update($request->validated());

        $projectApplication->load([
            'studentProfile.user',
            'project.customer:id,username,description,avatar,cover_image',
            'project' => fn ($q) => $q->withCount('applications'),
        ]);

        return response()->json([
            'message' => 'Project application updated successfully.',
            'data' => new ProjectApplicationResource($projectApplication),
        ]);
    }

    /**
     * Delete application (Student only - own application).
     */
    public function destroy(Request $request, ProjectApplication $projectApplication): JsonResponse
    {
        $user = $request->user('api');

        // Ensure the student owns this application
        $profile = $user->studentProfile;

        abort_if(
            ! $profile || (int) $projectApplication->student_profile_id !== (int) $profile->id,
            403,
            'This action is unauthorized.'
        );

        $projectApplication->delete();

        return response()->json([
            'message' => 'Project application deleted successfully.',
        ]);
    }
}
