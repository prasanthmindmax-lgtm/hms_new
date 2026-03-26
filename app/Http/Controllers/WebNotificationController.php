<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebNotificationController extends Controller
{
    /** GET /notifications/unread — polled by the bell icon every 30s */
    public function unread(Request $request)
    {
        $userId = auth()->id(); // null if not authenticated

        $notifications = DB::table('web_notifications')
            ->where(function ($q) use ($userId) {
                $q->whereNull('user_id');       // broadcast (all users)
                if ($userId) {
                    $q->orWhere('user_id', $userId); // or this specific user
                }
            })
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(50)
            ->get(['id', 'title', 'message', 'type', 'record_number',
                   'url', 'due_status', 'due_date', 'created_at']);

        return response()->json([
            'count'         => $notifications->count(),
            'notifications' => $notifications,
        ]);
    }

    /** POST /notifications/{id}/read */
    public function markRead(int $id)
    {
        DB::table('web_notifications')->where('id', $id)->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    /** POST /notifications/read-all */
    public function markAllRead(Request $request)
    {
        $userId = auth()->id();
        DB::table('web_notifications')
            ->where(function ($q) use ($userId) {
                $q->whereNull('user_id');
                if ($userId) $q->orWhere('user_id', $userId);
            })
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }
}