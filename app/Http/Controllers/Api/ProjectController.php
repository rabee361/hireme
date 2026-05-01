<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    /**
     * @var array<int, string>
     */
    private const PROJECT_PAYLOAD_FIELDS = [
        'id',
        'name',
        'details',
        'tool1',
        'tool2',
        'tool3',
        'tool4',
        'tool5',
        'cover_image',
        'customer_id',
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
            'data' => $projects->map(fn (Project $project): array => $this->projectPayload($project))->all(),
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
            'data' => $this->projectPayload($project),
        ], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load('customer:id,'.implode(',', array_slice(self::CUSTOMER_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Project retrieved successfully.',
            'data' => $this->projectPayload($project),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project->fill($request->validated());
        $project->save();

        $project->load('customer:id,'.implode(',', array_slice(self::CUSTOMER_RELATION_COLUMNS, 1)))->loadCount('applications');

        return response()->json([
            'message' => 'Project updated successfully.',
            'data' => $this->projectPayload($project),
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

    /**
     * @return array<string, mixed>
     */
    private function projectPayload(Project $project): array
    {
        $customer = $project->customer;
        $payload = $project->only(self::PROJECT_PAYLOAD_FIELDS);

        $payload['applicants_count'] = (int) ($project->applications_count ?? 0);
        $payload['customer'] = [
            'id' => $customer?->id,
            'name' => $customer?->username,
            'description' => $customer?->description,
            'image' => $customer?->avatar ?? $customer?->cover_image,
        ];
        $payload['since'] = $this->since($project);
        $payload['created_at'] = $project->created_at?->toISOString();
        $payload['updated_at'] = $project->updated_at?->toISOString();

        return $payload;
    }

    private function since(Project $project): ?string
    {
        if (! $project->created_at) {
            return null;
        }

        $days = $project->created_at->diffInDays(now());

        if ($days >= 1) {
            return $days.' '.Str::plural('day', $days).' ago';
        }

        $hours = $project->created_at->diffInHours(now());

        if ($hours >= 1) {
            return $hours.' '.Str::plural('hour', $hours).' ago';
        }

        return 'less than an hour ago';
    }
}