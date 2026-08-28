<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternalNotification;
use App\Services\NotificationService;
use App\Helpers\Qs;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::id();
        $query = InternalNotification::forUser($user_id)->latest();

        $filter = $request->get('filter', 'all');
        
        if ($filter == 'unread') {
            $query->unread();
        } elseif ($filter == 'urgent') {
            $query->whereIn('priority', ['high', 'urgent']);
        }

        $notifications = $query->paginate(20);

        return view('pages.support_team.notifications.index', compact('notifications', 'filter'));
    }

    public function getUnread()
    {
        $user_id = Auth::id();
        
        $count = NotificationService::getUnreadCount($user_id);
        $recent = NotificationService::getRecent($user_id, 5);
        
        return response()->json([
            'count' => $count,
            'notifications' => $recent
        ]);
    }

    public function markAsRead($id)
    {
        $notification = InternalNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->markAsRead();
        
        if(request()->ajax()){
            return response()->json(['success' => true]);
        }
        
        return back();
    }

    public function markAllAsRead()
    {
        InternalNotification::markAllAsRead(Auth::id());
        
        if(request()->ajax()){
            return response()->json(['success' => true]);
        }
        
        return back()->with('flash_success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy($id)
    {
        $notification = InternalNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->delete();
        
        return back()->with('flash_success', 'Notification supprimée.');
    }

    public function checkAlerts()
    {
        NotificationService::checkAndCreateAlerts();
        return back()->with('flash_success', 'Vérification des alertes terminée.');
    }
}
