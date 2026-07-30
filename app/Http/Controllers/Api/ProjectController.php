<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const CUSTOMER_RELATION_COLUMNS = [
        'id',
        'username',
        'description',
        'avatar',
        'cover_image',
    ];

    public function index(): JsonResponse
    {
        $projects = Project::query()
            ->with(['customer:id,'.implode(',', array_slice(self::CUSTOMER_RELATION_COLUMNS, 1))])
            ->withCount('applications')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Projects retrieved successfully.',
            'data' => ProjectResource::collection($projects),
        ]);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::query()->create([
            ...$request->validated(),
            'customer_id' => $request->user('api')->id,
        ]);

        $project->load('customer:id,'.implode(',', array_slice(self::CUSTOMER_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Project created successfully.',
            'data' => new ProjectResource($project),
        ], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load('customer:id,'.implode(',', array_slice(self::CUSTOMER_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Project retrieved successfully.',
            'data' => new ProjectResource($project),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $user = $request->user('api');

        abort_if(
            ! $user || $user->type !== UserType::Customer || (int) $project->customer_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        $project->fill($request->validated());
        $project->save();

        $project->load('customer:id,'.implode(',', array_slice(self::CUSTOMER_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => new ProjectResource($project),
        ]);
    }

    public function destroy(Request $request, Project $project): JsonResponse
    {
        $user = $request->user('api');

        abort_if(
            ! $user || $user->type !== UserType::Customer || (int) $project->customer_id !== (int) $user->id,
            403,
            'This action is unauthorized.'
        );

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }

    public function myProjects(Request $request): JsonResponse
    {
        $user = $request->user('api');

        abort_if(! $user || $user->type !== UserType::Customer, 403, 'This action is unauthorized.');

        $projects = Project::query()
            ->where('customer_id', $user->id)
            ->with(['customer:id,'.implode(',', array_slice(self::CUSTOMER_RELATION_COLUMNS, 1))])
            ->withCount('applications')
            ->latest()
            ->get();

        return response()->json([
            'message' => 'My projects retrieved successfully.',
            'data' => ProjectResource::collection($projects),
        ]);
    }
}
