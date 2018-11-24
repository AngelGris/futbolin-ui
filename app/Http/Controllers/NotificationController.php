<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notification;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;

            return response()->json([
                'notifications' => $user->notifications
            ], 200);
        } else {
            $vars = [
                'icon' => 'fa fa-envelope',
                'title' => __('headers.notifications_title'),
                'subtitle' => __('headers.notifications_subtitle')
            ];

            return view('notification.index', $vars);
        }
    }

    public function show(Request $request, Notification $notification)
    {
        if ($request->expectsJson() && !empty(Auth::guard('api')->user()->user)) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        if ($user->id == $notification->user_id) {
            if (is_null($notification->read_on)) {
                $notification->read_on = Carbon::now();
                $notification->save();
            }

            return response()->json([
                'title' => $notification->title,
                'message' => $notification->message,
                'unread' => $user->unreadMessages,
            ], 200);
        } else {
            return FALSE;
        }
    }
}
