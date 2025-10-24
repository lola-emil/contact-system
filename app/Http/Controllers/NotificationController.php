<?php

namespace App\Http\Controllers;

use App\Http\Services\NotificationService;

class NotificationController extends Controller
{

    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function getUserNotifications($userId)
    {
        $notifications = $this->notificationService->getUserNotification($userId);
        return response()->json($notifications);
    }

    public function deleteNotification($id)
    {
        $result = $this->deleteNotification($id);

        return response()->json($result);
    }

    public function clearNotification($userId) {
        $result = $this->clearNotification($userId);
        return response()->json($result);
    }
    
    public function markAsRead($id) {
        $result = $this->markAsRead($id);
        return response()->json($result);
    }

    public function markAllAsRead($userId) {
        $result = $this->markAllAsRead($userId);
        return response()->json($result);
    }
}
