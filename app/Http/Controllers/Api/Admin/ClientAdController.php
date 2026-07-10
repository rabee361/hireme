<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class ClientAdController extends Controller
{
    private AdminNotificationService $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tab 1: Get pending projects needing approval.
     */
    public function getPendingProjects()
    {
        $projects = Project::where('status', 'pending')->with('customer')->get();
        return response()->json(['data' => $projects]);
    }

    /**
     * Tab 2: Get approved projects.
     */
    public function getApprovedProjects()
    {
        $projects = Project::where('status', 'approved')->with('customer')->get();
        return response()->json(['data' => $projects]);
    }

    /**
     * Approve a project.
     */
    public function approveProject(Project $project)
    {
        if ($project->status !== 'pending') {
            return response()->json(['message' => 'Project is not pending.'], 400);
        }

        $project->update(['status' => 'approved']);

        // Send notification to client
        $this->notificationService->projectApproved($project->customer, $project->name);

        return response()->json(['message' => 'Project approved successfully.']);
    }

    /**
     * Reject a project.
     */
    public function rejectProject(Project $project)
    {
        if ($project->status !== 'pending') {
            return response()->json(['message' => 'Project is not pending.'], 400);
        }

        $client = $project->customer;
        $projectName = $project->name;

        $project->update(['status' => 'rejected']);
        $project->delete(); // 'رفض وحذف'

        // Send notification to client
        $this->notificationService->projectRejected($client, $projectName);

        return response()->json(['message' => 'Project rejected and deleted successfully.']);
    }

    /**
     * Delete an approved project from Tab 2.
     */
    public function deleteProject(Project $project)
    {
        if ($project->status !== 'approved') {
            return response()->json(['message' => 'Can only delete approved projects from this tab.'], 400);
        }

        $project->delete();
        return response()->json(['message' => 'Project deleted successfully.']);
    }
}
