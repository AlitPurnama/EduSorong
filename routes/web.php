<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationFeedController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\WithdrawalRequestController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    $campaigns = \App\Models\Campaign::with('organizationVerification')->latest()->limit(3)->get();
    return view("welcome", compact('campaigns'));
});

// Donation feed API (public, for running text)
Route::get('/api/donations/recent', [DonationFeedController::class, 'getRecentDonations'])->name('api.donations.recent');

// Public campaign routes
Route::get("/kampanye", [CampaignController::class, "index"])->name(
    "campaigns.index",
);
Route::get("/kampanye/{campaign}", [CampaignController::class, "show"])->name(
    "campaigns.show",
);

// Public user profile route
Route::get("/users/{user}", [
    \App\Http\Controllers\UserController::class,
    "show",
])->name("users.show");

// Auth routes
Route::get("/auth", [AuthController::class, "show"])->name("auth.show");
Route::get("/login", function () {
    return redirect()->route("auth.show", ["mode" => "login"]);
})->name("login");
Route::get("/register", function () {
    return redirect()->route("auth.show", ["mode" => "register"]);
})->name("register");

Route::post("/login", [AuthController::class, "login"])->name("auth.login");
Route::post("/register", [AuthController::class, "register"])->name(
    "auth.register",
);
// Email verification route (public, accessible via email link even when logged in)
Route::get("/verify-email/{token}", [AuthController::class, "verifyEmail"])->name("auth.verify-email");
// Account deletion confirmation route (public, accessible via email link)
Route::get("/pengaturan/akun/hapus/{token}", [
    UserSettingsController::class,
    "confirmAccountDeletion",
])->name("settings.account.delete-confirm");
Route::post("/logout", [AuthController::class, "logout"])->name("logout");

// Dashboard & campaign management
Route::middleware("auth")->group(function () {
    Route::get("/dashboard", [CampaignController::class, "dashboard"])->name(
        "dashboard",
    );
    Route::get("/dashboard/kampanye/create", [
        CampaignController::class,
        "create",
    ])->name("dashboard.campaigns.create");
    Route::post("/dashboard/kampanye", [
        CampaignController::class,
        "store",
    ])->name("dashboard.campaigns.store");
    Route::get("/dashboard/kampanye/{campaign}/hapus", [
        CampaignController::class,
        "requestDeletion",
    ])->name("dashboard.campaigns.request-deletion");
    Route::delete("/dashboard/kampanye/{campaign}", [
        CampaignController::class,
        "destroy",
    ])->name("dashboard.campaigns.destroy");

    // User settings
    Route::get("/pengaturan", [UserSettingsController::class, "show"])->name(
        "settings.show",
    );
    Route::put("/pengaturan/profil", [
        UserSettingsController::class,
        "updateProfile",
    ])->name("settings.profile.update");
    Route::put("/pengaturan/password", [
        UserSettingsController::class,
        "updatePassword",
    ])->name("settings.password.update");
    Route::post("/pengaturan/email/resend-verification", [
        UserSettingsController::class,
        "resendEmailVerification",
    ])->name("settings.email.resend-verification");
    Route::put("/pengaturan/email", [
        UserSettingsController::class,
        "updateEmail",
    ])->name("settings.email.update");
    Route::delete("/pengaturan/akun", [
        UserSettingsController::class,
        "deleteAccount",
    ])->name("settings.account.delete");
    Route::get("/pengaturan/ktp", [
        UserSettingsController::class,
        "showKtpVerification",
    ])->name("settings.ktp.show");
    Route::post("/pengaturan/ktp", [
        UserSettingsController::class,
        "submitKtpVerification",
    ])->name("settings.ktp.submit");

    // Organization Verification Routes
    Route::prefix('organisasi')->name('organization.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrganizationVerificationController::class, 'index'])->name('index');
        Route::get('/verifikasi', [\App\Http\Controllers\OrganizationVerificationController::class, 'create'])->name('create');
        Route::post('/verifikasi', [\App\Http\Controllers\OrganizationVerificationController::class, 'store'])->name('store');
        Route::get('/{organizationVerification}', [\App\Http\Controllers\OrganizationVerificationController::class, 'show'])->name('show');
    });

    // Withdrawal Requests (User can request withdrawal for their campaigns)
    Route::prefix('withdrawal')->name('withdrawal.')->group(function () {
        Route::get('/kampanye/{campaign}/request', [WithdrawalRequestController::class, 'create'])->name('create');
        Route::post('/kampanye/{campaign}/request', [WithdrawalRequestController::class, 'store'])->name('store');
        Route::get('/request/{withdrawal}', [WithdrawalRequestController::class, 'show'])->name('show');
        
        // Withdrawal Evidence
        Route::get('/request/{withdrawal}/evidence/create', [\App\Http\Controllers\WithdrawalEvidenceController::class, 'create'])->name('evidence.create');
        Route::post('/request/{withdrawal}/evidence', [\App\Http\Controllers\WithdrawalEvidenceController::class, 'store'])->name('evidence.store');
        Route::delete('/request/{withdrawal}/evidence/{evidence}', [\App\Http\Controllers\WithdrawalEvidenceController::class, 'destroy'])->name('evidence.destroy');
    });

    // Campaign Updates
    Route::post('/kampanye/{campaign}/updates', [\App\Http\Controllers\CampaignUpdateController::class, 'store'])->name('campaigns.updates.store');
    Route::delete('/kampanye/{campaign}/updates/{update}', [\App\Http\Controllers\CampaignUpdateController::class, 'destroy'])->name('campaigns.updates.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Organization Verifications
    Route::get('/verifications', [AdminController::class, 'verifications'])->name('verifications');
    Route::post('/verifications/{verification}/approve', [AdminController::class, 'approveVerification'])->name('verifications.approve');
    Route::post('/verifications/{verification}/reject', [AdminController::class, 'rejectVerification'])->name('verifications.reject');
    
    // Withdrawal Requests
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/withdrawals/{withdrawal}/approve', [AdminController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/withdrawals/{withdrawal}/reject', [AdminController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
    Route::post('/withdrawals/{withdrawal}/complete', [AdminController::class, 'completeWithdrawal'])->name('withdrawals.complete');
    
    // KTP Verifications
    Route::get('/ktp-verifications', [AdminController::class, 'ktpVerifications'])->name('ktp-verifications');
    Route::post('/ktp-verifications/{user}/approve', [AdminController::class, 'approveKtpVerification'])->name('ktp-verifications.approve');
    Route::post('/ktp-verifications/{user}/reject', [AdminController::class, 'rejectKtpVerification'])->name('ktp-verifications.reject');
    
    // Withdrawal Evidence Verification
    Route::post('/evidences/{evidence}/verify', [AdminController::class, 'verifyEvidence'])->name('evidences.verify');
    
    // Campaign Deletion Requests
    Route::get('/deletion-requests', [AdminController::class, 'deletionRequests'])->name('deletion-requests');
    Route::post('/deletion-requests/{deletionRequest}/approve', [AdminController::class, 'approveDeletionRequest'])->name('deletion-requests.approve');
    Route::post('/deletion-requests/{deletionRequest}/reject', [AdminController::class, 'rejectDeletionRequest'])->name('deletion-requests.reject');
});

// Payment routes (can be accessed by guests for donations)
Route::prefix('payment')->name('payment.')->group(function () {
    // Create payment methods (guests can donate)
    Route::post('/kampanye/{campaign}/qris', [PaymentController::class, 'createQRIS'])->name('qris.create');
    Route::post('/kampanye/{campaign}/ewallet', [PaymentController::class, 'createEWallet'])->name('ewallet.create');
    Route::post('/kampanye/{campaign}/virtual-account', [PaymentController::class, 'createVirtualAccount'])->name('va.create');
    
    // Get payment status (public)
    Route::get('/{payment}/status', [PaymentController::class, 'getStatus'])->name('status');
    
    // Payment result pages (public)
    Route::get('/{payment}/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/{payment}/failed', [PaymentController::class, 'failed'])->name('failed');
});

// Notification route (no auth required, but should be protected by Midtrans signature)
// Support both /notification/midtrans and /notifications/midtrans for compatibility
Route::post('/notification/midtrans', [NotificationController::class, 'handle'])->name('payment.notification');
Route::post('/notifications/midtrans', [NotificationController::class, 'handle'])->name('payment.notification.alternative');
