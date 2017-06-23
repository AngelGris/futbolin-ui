<?php

namespace App\Http\Middleware;

use Closure;
use App\AdminMessage;

class UserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::check()) {
            $user = \Auth::user();

            $request_time = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
            $message = AdminMessage::where('valid_from', '>', $user->last_activity ? $user->last_activity : 0)->where('valid_from', '<', $request_time)->where('valid_to', '>', $request_time)->latest()->first();

            if ($message) {
                \Session::flash('admin_message', $message['id']);
            } else {
                \Session::forget('admin_message');
            }
            \DB::table('users')->where('id', '=', $user->id)->update(['last_activity' => date('Y-m-d H:i:s')]);
        }

        return $next($request);
    }
}
