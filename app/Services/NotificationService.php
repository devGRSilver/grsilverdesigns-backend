<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Get latest unread notifications for the logged-in user
     *
     * @param int $limit
     * @return array
     */
    public function getUnreadNotifications(int $limit = 10): array
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->latest()
            ->take($limit)
            ->get();

        return $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'url' => $notification->url ?? NULL,
                'created_at' => $notification->created_at->diffForHumans(),
            ];
        })->toArray();
    }

    /**
     * Mark a single notification as read
     *
     * @param int $id
     * @return bool
     */
    public function markAsRead(int $id): bool
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if ($notification && $notification->read_at === null) {
            $notification->update(['read_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read
     *
     * @return bool
     */
    public function markAllRead(): bool
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return true;
    }


    public function createNotification(int $userId, string $title, ?string $message = null, ?string $url = null, ?string $type = null): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'url' => $url,
        ]);
    }

    /**
     * Get total unread notifications count
     *
     * @return int
     */
    public function getUnreadCount(): int
    {
        return Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }
}
