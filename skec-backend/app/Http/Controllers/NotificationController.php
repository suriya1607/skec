<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * List all notifications for the authenticated student (newest first).
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::with(['note:id,title,slug', 'announcement:id,title,type'])
            ->forUser($request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->paginatedResponse($notifications);
    }

    /**
     * Return the count of unread notifications for the authenticated student.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::forUser($request->user()->id)
            ->unread()
            ->count();

        return $this->success(['count' => $count]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $notification->markAsRead();

        return $this->success($notification, 'Notification marked as read');
    }

    /**
     * Mark all notifications as read for the authenticated student.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        Notification::forUser($request->user()->id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return $this->success(null, 'All notifications marked as read');
    }
}
