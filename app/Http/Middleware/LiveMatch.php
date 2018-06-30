<?php

namespace App\Http\Middleware;

use Closure;

class LiveMatch
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
        if ($request->expectsJson() && !empty(\Auth::guard('api')->user())) {
            $team = \Auth::guard('api')->user()->user->team;

            if ($team && $team->live_match) {
                return response()->json([
                    'errors' => [
                        'type'      => 'live_match',
                        'message'   => 'Broadcasting live match'
                    ]
                ], 400);
            }
        } else {
            if (\Auth::check()) {
                $team = \Auth::user()->team;

                if ($team && $team->live_match) {
                    return redirect()->route('match.live');
                }
            }
        }

        return $next($request);
    }
}
