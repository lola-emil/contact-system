<?php

namespace App\Http\Services;

use App\Models\Notification;
use App\Models\SharedContact;

class NotificationService
{


    public function getUserNotification(int $userId)
    {
        $notifications = Notification::where("user_id", $userId)->get();
        return $notifications;
    }

    public function addNewSharedContactNotification($senderId, $receiverId, $contactId)
    {
        $matchedSharedContact = SharedContact::with([
            "contact.owner"
        ])
            ->where("user_id", $receiverId)
            ->where("contact_id", $contactId)
            ->first();

        $newNotif = new Notification([
            "title" => strtoupper("{$matchedSharedContact->contact->owner->firstname} {$matchedSharedContact->contact->owner->lastname}"),
            "message" => "Shared a contact for {$matchedSharedContact->contact->firstname} {$matchedSharedContact->contact->lastname}",
            "category" => "info",
            "actionable" => true,
            "user_id" => $receiverId
        ]);

        $newNotif->save();

        return $newNotif;
    }

    public function addNewNotif(
        $title = "",
        $message = "",
        $category = "info",
        bool $actionable,
        $userId
    ) {
        $newNotif = new Notification([
            "title" => $title,
            "message" => $message,
            "user_id" => $userId,
            "category" => $category,
            "actionable" => $actionable
        ]);

        $newNotif->save();

        return $newNotif;
    }

    // for deleting all the notifications
    public function clearNotifications($id)
    {
        $notification = Notification::find($id);
        $notification->delete();
        return $notification;
    }

    public function deleteNotification($userId)
    {
        $result = Notification::where("user_id", $userId)->delete();

        return $result;
    }

    public function markAsRead($id)
    {
        $result = Notification::find($id);
        $result->is_read = true;

        return $result;
    }

    public function markAllAsRead($userId)
    {
        $result = Notification::where("user_id", $userId)->update([
            "is_read" => true
        ]);
        return $result;
    }
}
