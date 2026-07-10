<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdApplication;
use App\Services\AdminNotificationService;
use Illuminate\Http\Request;

class CompanyAdController extends Controller
{
    private AdminNotificationService $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tab 1: Get pending ads needing approval.
     */
    public function getPendingAds()
    {
        $ads = Ad::where('status', 'pending')->with('company')->get();
        return response()->json(['data' => $ads]);
    }

    /**
     * Tab 2: Get approved ads.
     */
    public function getApprovedAds()
    {
        $ads = Ad::where('status', 'approved')->with('company')->get();
        return response()->json(['data' => $ads]);
    }

    /**
     * Approve an ad.
     */
    public function approveAd(Ad $ad)
    {
        if ($ad->status !== 'pending') {
            return response()->json(['message' => 'Ad is not pending.'], 400);
        }

        $ad->update(['status' => 'approved']);

        // Send notification to company
        $this->notificationService->companyAdApproved($ad->company, $ad->job_name);

        return response()->json(['message' => 'Ad approved successfully.']);
    }

    /**
     * Reject an ad.
     */
    public function rejectAd(Ad $ad)
    {
        if ($ad->status !== 'pending') {
            return response()->json(['message' => 'Ad is not pending.'], 400);
        }

        $company = $ad->company;
        $jobName = $ad->job_name;

        // Optionally, you could soft-delete or just set status to rejected.
        $ad->update(['status' => 'rejected']);
        $ad->delete(); // The user's prompt says 'رفض وحذف'

        // Send notification to company
        $this->notificationService->companyAdRejected($company, $jobName);

        return response()->json(['message' => 'Ad rejected and deleted successfully.']);
    }

    /**
     * Delete an approved ad from Tab 2.
     */
    public function deleteAd(Ad $ad)
    {
        if ($ad->status !== 'approved') {
            return response()->json(['message' => 'Can only delete approved ads from this tab.'], 400);
        }

        $ad->delete();
        return response()->json(['message' => 'Ad deleted successfully.']);
    }

    /**
     * Send reminder to company about an accepted student.
     */
    public function sendReminder(AdApplication $application)
    {
        $ad = $application->ad;
        $student = $application->studentProfile->student;
        $company = $ad->company;

        $this->notificationService->companyReminder($company, $student->name ?? 'Student', $ad->job_name);

        return response()->json(['message' => 'Reminder sent successfully.']);
    }
}
