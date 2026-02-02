<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ResponseController;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends ResponseController
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get the latest unread notifications
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = $this->notificationService->getUnreadNotifications();

        return $this->successResponse([
            'count' => count($data),
            'data'  => $data
        ], 'Notification List');
    }

    /**
     * Mark a single notification as read
     *
     * @param int $id
     * @return JsonResponse
     */
    public function markAsRead(int $id): JsonResponse
    {
        if ($this->notificationService->markAsRead($id)) {
            return $this->successResponse([], 'Notification marked as read.');
        }

        return $this->errorResponse('Notification not found.', 404);
    }

    /**
     * Mark all notifications as read
     *
     * @return JsonResponse
     */
    public function markAllRead(): JsonResponse
    {
        $this->notificationService->markAllRead();

        return $this->successResponse([], 'All notifications marked as read.');
    }
}
