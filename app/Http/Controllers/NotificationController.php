<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $vars = [
            'icon' => 'fa fa-envelope',
            'title' => 'Notificaciones',
            'subtitle' => 'Las notificaciones no se autodestruirán'
        ];

        return view('notification.index', $vars);
    }

    public function show(Notification $notification)
    {
        $user = Auth::user();

        if ($user->id == $notification->user_id) {
            $notification->read_on = date('Y-m-d H:i:s');
            $notification->save();

            return json_encode([
                'title' => $notification->title,
                'message' => $notification->message,
                'unread' => $user->unreadMessages,
            ]);
        } else {
            return FALSE;
        }
    }
}
