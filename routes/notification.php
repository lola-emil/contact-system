<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group([
    "prefix" => "notifications",
    "middleware" => "auth"
], function () {
    Route::get("/get-user-notifications/{id}", [NotificationController::class, "getUserNotifications"]);

    Route::delete("/delete-notification/{id}", [NotificationController::class, "deleteNotification"]);
    Route::delete("/clear-notifications/{userId}", [NotificationController::class, "clearNotification"]);
    
    Route::patch("/mark-as-read/{id}", [NotificationController::class, "markAsRead"]);
    Route::patch("/mark-all-as-read/{userId}", [NotificationController::class, "markAllasRead"]);
});
