<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/notifications
     * Paginated list of the authenticated user's notifications (newest first).
     */
    public function index(Request $request): JsonResponse
    {
        
        $notifications = Notification::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate($request->input('per_page', 15));

        return response()->json($notifications);
    }

    /**
     * GET /api/notifications/unread-count
     * Returns the count of unread notifications.
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * PATCH /api/notifications/{id}/read
     * Mark a single notification as read.
     */
    public function markRead(int $id): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->update(['is_read' => true]);

        return response()->json([
            'message'      => 'Notification marked as read.',
            'notification' => $notification,
        ]);
    }

    /**
     * PATCH /api/notifications/read-all
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllRead(): JsonResponse
    {
        $updated = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => "Marked {$updated} notification(s) as read.",
            'updated' => $updated,
        ]);
    }

    /**
     * DELETE /api/notifications/{id}
     * Delete a specific notification belonging to the authenticated user.
     */
    public function destroy(int $id): JsonResponse
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }
}
