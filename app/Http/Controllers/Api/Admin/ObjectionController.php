<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Objection;
use App\Services\AdminNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ObjectionController extends Controller
{
    private AdminNotificationService $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all objections.
     */
    public function index()
    {
        $objections = Objection::with(['customer', 'projectApplication.project', 'projectApplication.studentProfile.student'])->get();
        return response()->json(['data' => $objections]);
    }

    /**
     * Admin rejects the client's objection (student is right).
     */
    public function rejectObjection(Objection $objection)
    {
        $objection->update(['status' => 'resolved']);
        $application = $objection->projectApplication;
        $client = $objection->customer;
        $studentName = $application->studentProfile->student->name ?? 'Student';
        $projectName = $application->project->name;

        // Notify client
        $this->notificationService->clientObjectionRejected($client, $projectName, $studentName);

        // Delete objection from list (per user prompt)
        $objection->delete();

        return response()->json(['message' => 'Objection rejected. Client notified.']);
    }

    /**
     * Admin accepts the client's objection (student needs to fix).
     */
    public function acceptObjection(Objection $objection)
    {
        $objection->update(['status' => 'reviewed']); // Or 'accepted'
        $application = $objection->projectApplication;
        
        $client = $objection->customer;
        $student = $application->studentProfile->student;
        $projectName = $application->project->name;
        $studentName = $student->name ?? 'Student';

        // Notify client
        $this->notificationService->clientObjectionAccepted($client, $projectName, $studentName);

        // Notify student to fix within 48h
        $deadline = Carbon::now()->addHours(48);
        $deadlineStr = $deadline->format('Y/m/d');
        
        $this->notificationService->studentObjectionReceived(
            $student, 
            $client->name ?? 'Client', 
            $projectName, 
            $objection->description, 
            $deadlineStr, 
            $application->submission_link
        );

        return response()->json(['message' => 'Objection accepted. Student notified to fix within 48 hours.']);
    }

    /**
     * Admin reviews the student's fixed submission.
     */
    public function reviewFixSubmission(Objection $objection, Request $request)
    {
        $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $application = $objection->projectApplication;
        $client = $objection->customer;
        $student = $application->studentProfile->student;
        $projectName = $application->project->name;
        $link = $application->submission_link;

        if ($request->is_approved) {
            // Fix is good, send back to client for trial
            $this->notificationService->studentFixApproved($student, $link, $client->name ?? 'Client');
            $this->notificationService->clientFixReceived($client, $student->name ?? 'Student', $projectName, $link);

            // Delete objection since it is resolved
            $objection->delete();

            return response()->json(['message' => 'Fix approved and sent to client.']);
        } else {
            // Fix is bad, student needs to try again if there is time
            // Assuming 48h deadline is stored or checked elsewhere
            $deadlineDate = 'N/A'; // Ideally calculate based on when objection was accepted
            $this->notificationService->studentFixRejected($student, $link, $deadlineDate);

            return response()->json(['message' => 'Fix rejected. Student notified.']);
        }
    }
}
