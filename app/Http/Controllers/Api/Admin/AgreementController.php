<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectApplication;
use App\Services\AdminNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    private AdminNotificationService $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all agreements (project applications that are accepted/in progress).
     */
    public function index()
    {
        $agreements = ProjectApplication::whereIn('status', [
            'accepted_by_client', 'in_progress', 'delivered_to_admin', 'delivered_to_customer', 'completed'
        ])->with(['project.customer', 'studentProfile.student'])->get();

        return response()->json(['data' => $agreements]);
    }

    /**
     * Admin confirms the start of the agreement after client has paid.
     * Generates link, sets dates, and notifies student to start working.
     */
    public function startAgreement(ProjectApplication $application)
    {
        if ($application->status !== 'accepted_by_client') {
            return response()->json(['message' => 'Agreement is not waiting for admin confirmation.'], 400);
        }

        // Logic to generate a link for submission
        $submissionLink = "https://hiremee.app/submit/" . uniqid();

        $deliveryDays = $application->project->delivery_days ?? 3; // default or from project
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays($deliveryDays);

        $application->update([
            'status' => 'in_progress',
            'client_approval_date' => $startDate,
            'delivery_deadline_date' => $endDate,
            'submission_link' => $submissionLink,
        ]);

        $student = $application->studentProfile->student;
        $client = $application->project->customer;

        // Notification 1: Client accepted
        $this->notificationService->studentAgreementStarted(
            $student, 
            $client->name ?? 'Client', 
            $application->project->name, 
            $application->expected_salary . ' $'
        );

        // Notification 2: Admin confirms start with link and dates
        $this->notificationService->studentConfirmStartWithLink(
            $student,
            $client->name ?? 'Client',
            $application->project->name,
            $submissionLink,
            $startDate->format('Y/m/d'),
            $endDate->format('Y/m/d')
        );

        return response()->json(['message' => 'Agreement started successfully. Notifications sent.']);
    }

    /**
     * Admin reviews student's submitted work.
     */
    public function reviewSubmission(ProjectApplication $application, Request $request)
    {
        $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $student = $application->studentProfile->student;
        $client = $application->project->customer;
        $projectName = $application->project->name;
        $link = $application->submission_link;

        if ($request->is_approved) {
            // Admin approved submission, send to client for trial
            $trialEndsAt = Carbon::now()->addDays(7); // 7 days trial period

            $application->update([
                'status' => 'delivered_to_customer',
                'trial_ends_at_date' => $trialEndsAt,
            ]);

            // Notify Student
            $this->notificationService->studentSubmissionApprovedByAdmin($student, $link, $client->name ?? 'Client');

            // Notify Client
            $this->notificationService->clientReceivedService($client, $projectName, $student->name ?? 'Student', $link);

            return response()->json(['message' => 'Submission approved. Sent to client for trial.']);
        } else {
            // Admin rejected submission, ask student to fix
            $application->update(['status' => 'in_progress']); // Send back to progress
            $endDate = $application->delivery_deadline_date ? $application->delivery_deadline_date->format('Y/m/d') : 'N/A';

            $this->notificationService->studentSubmissionRejectedByAdmin($student, $link, $endDate);

            return response()->json(['message' => 'Submission rejected. Student notified to fix.']);
        }
    }

    /**
     * Handle if student fails to deliver on time.
     */
    public function handleFailedDelivery(ProjectApplication $application)
    {
        $student = $application->studentProfile->student;
        $client = $application->project->customer;
        $projectName = $application->project->name;

        $application->update(['status' => 'cancelled']);

        $this->notificationService->studentFailedDelivery($student, $projectName, $client->name ?? 'Client');
        $this->notificationService->clientRefundedStudentFailed($client, $student->name ?? 'Student', $projectName, $application->expected_salary . ' $');

        return response()->json(['message' => 'Agreement cancelled due to failed delivery. Notifications sent.']);
    }

    /**
     * Trial period ended successfully with no objections.
     */
    public function finalizeTrial(ProjectApplication $application)
    {
        if ($application->status !== 'delivered_to_customer') {
            return response()->json(['message' => 'Not in trial period.'], 400);
        }

        $student = $application->studentProfile->student;
        $client = $application->project->customer;

        $application->update(['status' => 'completed']);

        // Send payment logic goes here...

        $this->notificationService->studentTrialEndedSuccessfully($student, $application->project->name, $client->name ?? 'Client');

        return response()->json(['message' => 'Trial finalized successfully. Funds released.']);
    }
}
