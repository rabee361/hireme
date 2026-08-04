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

    /**
     * Accept a student's project application (Customer only).
     */
    public function acceptApplication(Request $request, ProjectApplication $projectApplication): JsonResponse
    {
        $user = $request->user('api');
        $project = $projectApplication->project;

        abort_if(
            ! $user || $user->type !== UserType::Customer || (int) $project->customer_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        if ($projectApplication->status !== 'pending' && $projectApplication->status !== 'not_assigned') {
            return response()->json(['message' => 'Application is not pending.'], 400);
        }

        $projectApplication->update(['status' => 'accepted_by_client']);

        $studentUser = $projectApplication->studentProfile->user;
        $customerUser = $project->customer;

        app(\App\Services\AdminNotificationService::class)->studentAgreementStarted(
            $studentUser,
            $customerUser->name ?? $customerUser->username ?? 'Client',
            $project->name,
            ($projectApplication->expected_salary ?? '0') . ' $'
        );

        return response()->json([
            'message' => 'Application accepted successfully.',
            'data' => new ProjectApplicationResource($projectApplication->refresh()),
        ]);
    }

    /**
     * Reject a student's project application (Customer only).
     */
    public function rejectApplication(Request $request, ProjectApplication $projectApplication): JsonResponse
    {
        $user = $request->user('api');
        $project = $projectApplication->project;

        abort_if(
            ! $user || $user->type !== UserType::Customer || (int) $project->customer_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        if ($projectApplication->status !== 'pending' && $projectApplication->status !== 'not_assigned') {
            return response()->json(['message' => 'Application is not pending.'], 400);
        }

        $projectApplication->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Application rejected successfully.',
            'data' => new ProjectApplicationResource($projectApplication->refresh()),
        ]);
    }

    /**
     * Get the list of accepted project applications for the customer.
     */
    public function receivedProjects(Request $request): JsonResponse
    {
        $user = $request->user('api');

        abort_if(
            ! $user || $user->type !== UserType::Customer,
            403,
            'This action is unauthorized.'
        );

        $eligibleApplications = ProjectApplication::whereHas('project', function ($query) use ($user) {
            $query->where('customer_id', $user->id);
        })
        ->whereIn('status', ['accepted_by_client', 'in_progress', 'delivered_to_admin', 'delivered_to_customer', 'completed'])
        ->with(['project', 'studentProfile.user'])
        ->latest()
        ->get()
        ->map(function ($application) {
            $studentUser = $application->studentProfile->user;
            return [
                'application_id' => $application->id,
                'project_name' => $application->project->name,
                'student_name' => $studentUser ? ($studentUser->name ?? $studentUser->username) : null,
                'status' => $application->status,
            ];
        });

        return response()->json([
            'data' => $eligibleApplications
        ]);
    }
}
