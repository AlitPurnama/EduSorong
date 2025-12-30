<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

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
    Route::get("/pengaturan/ktp", [
        UserSettingsController::class,
        "showKtpVerification",
    ])->name("settings.ktp.show");
});
