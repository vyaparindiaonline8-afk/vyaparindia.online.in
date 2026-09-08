<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    /**
     * Send a new notification to a user.
     *
     * @param User $user
     * @param string $type
     * @param array $data
     * @return Notification
     */
    public function sendNotification(User $user, string $type, array $data = []): Notification
    {
        return $user->notifications()->create([
            'type' => $type,
            'data' => $data,
        ]);
    }

    /**
     * Mark a specific notification as read.
     *
     * @param Notification $notification
     * @return bool
     */
    public function markAsRead(Notification $notification): bool
    {
        if (is_null($notification->read_at)) {
            $notification->read_at = now();
            return $notification->save();
        }
        return false;
    }

    /**
     * Get unread notifications for a user.
     *
     * @param User $user
     * @return Collection<int, Notification>
     */
    public function getUnreadNotifications(User $user): Collection
    {
        return $user->notifications()->whereNull('read_at')->get();
    }

    /**
     * Get all notifications for a user.
     *
     * @param User $user
     * @return Collection<int, Notification>
     */
    public function getAllNotifications(User $user): Collection
    {
        return $user->notifications()->get();
    }
}
