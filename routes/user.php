<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "users",
    "middleware" => "auth"
], function () {
    Route::get("/search-emails", [UserController::class, "searchEmails"]);
    Route::get("/check-email", [UserController::class, "checkEmail"]);
    Route::get("/get-emails", [UserController::class, "getEmails"]);

    Route::get("/profile", [UserController::class, "profile"]);


    Route::post("/check-email-availability", [UserController::class, "checkEmailAvailability"]);
    Route::post("/update-profile", [UserController::class, "updateProfile"]);

    Route::post("/update-password", [UserController::class, "updatePassword"]);
});
