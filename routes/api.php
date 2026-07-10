<?php

use App\Http\Controllers\Api\AdApplicationController;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\ProjectApplicationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\PushNotificationController;
use App\Http\Controllers\Api\SavedStudentController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\StudentSavedAdController;
use App\Http\Controllers\Api\Admin\CompanyAdController;
use App\Http\Controllers\Api\Admin\ClientAdController;
use App\Http\Controllers\Api\Admin\AgreementController;
use App\Http\Controllers\Api\Admin\ObjectionController as AdminObjectionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('send-email', [AuthController::class, 'sendEmail']);
    Route::post('change-password', [AuthController::class, 'changePassword']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::middleware(['auth:api', 'jwt.access'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::apiResource('ads', AdController::class)->only(['index', 'show']);
Route::apiResource('projects', ProjectController::class)->only(['index', 'show']);

Route::middleware(['auth:api', 'jwt.access'])->group(function () {
    // Profile routes (index, show, update)
    Route::apiResource('students', StudentController::class)->only(['index', 'show', 'update']);
    Route::apiResource('companies', CompanyController::class)->only(['index', 'show', 'update']);
    Route::apiResource('customers', CustomerController::class)->only(['index', 'show', 'update']);

    // Ad & Project routes (authenticated)
    Route::apiResource('ads', AdController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('projects', ProjectController::class)->only(['store', 'update', 'destroy']);

    // Company my-ads
    Route::get('my-ads', [AdController::class, 'myAds']);

    // Saved students (company saves student)
    Route::get('saved-students', [SavedStudentController::class, 'index']);
    Route::post('saved-students/{student}', [SavedStudentController::class, 'store']);
    Route::delete('saved-students/{student}', [SavedStudentController::class, 'destroy']);

    // Student saved ads
    Route::get('student/saved-ads', [StudentSavedAdController::class, 'index']);
    Route::post('student/saved-ads/{ad}', [StudentSavedAdController::class, 'store']);
    Route::delete('student/saved-ads/{ad}', [StudentSavedAdController::class, 'destroy']);

    // Student's own applications
    Route::get('student/my-ad-applications', [AdApplicationController::class, 'myApplications']);
    Route::get('student/my-project-applications', [ProjectApplicationController::class, 'myApplications']);

    // Ad & Project application CRUD
    Route::apiResource('ad-applications', AdApplicationController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('project-applications', ProjectApplicationController::class)->only(['store', 'update', 'destroy']);

    // Company/Customer review applications for their ads/projects
    Route::get('ads/{ad}/applications', [AdApplicationController::class, 'index']);
    Route::get('projects/{project}/applications', [ProjectApplicationController::class, 'index']);

    // Objections
    Route::get('objections/eligible', [\App\Http\Controllers\Api\ObjectionController::class, 'eligible']);
    Route::post('objections', [\App\Http\Controllers\Api\ObjectionController::class, 'store']);

    // Device tokens
    Route::get('device-tokens', [DeviceTokenController::class, 'index']);
    Route::post('device-tokens', [DeviceTokenController::class, 'store']);
    Route::delete('device-tokens/{deviceToken}', [DeviceTokenController::class, 'destroy']);

    // Push notifications
    Route::get('push-notifications', [PushNotificationController::class, 'index']);
    Route::patch('push-notifications/{pushNotificationRecipient}/read', [PushNotificationController::class, 'markAsRead']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
        // Company Ads
        Route::get('company-ads/pending', [CompanyAdController::class, 'getPendingAds']);
        Route::get('company-ads/approved', [CompanyAdController::class, 'getApprovedAds']);
        Route::post('company-ads/{ad}/approve', [CompanyAdController::class, 'approveAd']);
        Route::post('company-ads/{ad}/reject', [CompanyAdController::class, 'rejectAd']);
        Route::delete('company-ads/{ad}', [CompanyAdController::class, 'deleteAd']);
        Route::post('company-ads/applications/{application}/reminder', [CompanyAdController::class, 'sendReminder']);

        // Client Ads
        Route::get('client-ads/pending', [ClientAdController::class, 'getPendingProjects']);
        Route::get('client-ads/approved', [ClientAdController::class, 'getApprovedProjects']);
        Route::post('client-ads/{project}/approve', [ClientAdController::class, 'approveProject']);
        Route::post('client-ads/{project}/reject', [ClientAdController::class, 'rejectProject']);
        Route::delete('client-ads/{project}', [ClientAdController::class, 'deleteProject']);

        // Agreements
        Route::get('agreements', [AgreementController::class, 'index']);
        Route::post('agreements/{application}/start', [AgreementController::class, 'startAgreement']);
        Route::post('agreements/{application}/review', [AgreementController::class, 'reviewSubmission']);
        Route::post('agreements/{application}/failed-delivery', [AgreementController::class, 'handleFailedDelivery']);
        Route::post('agreements/{application}/finalize-trial', [AgreementController::class, 'finalizeTrial']);

        // Objections
        Route::get('objections', [AdminObjectionController::class, 'index']);
        Route::post('objections/{objection}/accept', [AdminObjectionController::class, 'acceptObjection']);
        Route::post('objections/{objection}/reject', [AdminObjectionController::class, 'rejectObjection']);
        Route::post('objections/{objection}/review-fix', [AdminObjectionController::class, 'reviewFixSubmission']);
    });
});
