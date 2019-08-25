<?php

namespace App\Http\Middleware;

use Closure;
use App\AdminMessage;
use Carbon\Carbon;

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

            $request_time = Carbon::now();
            $message = AdminMessage::where('valid_from', '>', $user->last_activity ? $user->last_activity->toDatetimeString() : 0)->where('valid_from', '<', $request_time)->where('valid_to', '>', $request_time)->latest()->first();

            if ($message) {
                \Session::flash('admin_message', $message['id']);
            } else {
                \Session::forget('admin_message');
            }
            $user->last_activity = Carbon::now();
            $user->ip_address = $request->ip();
            $user->save();
        }

        return $next($request);
    }
}
