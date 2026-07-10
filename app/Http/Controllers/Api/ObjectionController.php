<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Objection;
use App\Models\ProjectApplication;
use Illuminate\Http\JsonResponse;

class ObjectionController extends Controller
{
    /**
     * Get the list of services (project applications) the customer can object to.
     */
    public function eligible(Request $request): JsonResponse
    {
        $user = $request->user();

        // Only project applications that belong to the customer's projects 
        // and have status 'delivered_to_customer'
        $eligibleApplications = ProjectApplication::whereHas('project', function ($query) use ($user) {
            $query->where('customer_id', $user->id);
        })
        ->where('status', 'delivered_to_customer')
        ->with('project')
        ->get();

        return response()->json([
            'data' => $eligibleApplications
        ]);
    }

    /**
     * Submit an objection for a specific project application.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'project_application_id' => 'required|exists:project_applications,id',
            'description' => 'required|string',
        ]);

        $user = $request->user();

        // Verify that the project application belongs to a project owned by this user
        $application = ProjectApplication::whereHas('project', function ($query) use ($user) {
            $query->where('customer_id', $user->id);
        })->find($request->project_application_id);

        if (!$application) {
            return response()->json([
                'message' => 'Unauthorized or invalid project application.'
            ], 403);
        }

        // Verify the status is delivered_to_customer
        if ($application->status !== 'delivered_to_customer') {
            return response()->json([
                'message' => 'You can only object to services that have been delivered to you.'
            ], 400);
        }

        $objection = Objection::create([
            'customer_id' => $user->id,
            'project_application_id' => $application->id,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Objection submitted successfully.',
            'data' => $objection
        ], 201);
    }
}
