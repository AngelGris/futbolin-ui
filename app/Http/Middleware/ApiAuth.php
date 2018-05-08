<?php

namespace App\Http\Middleware;

use Closure;
use App\ApiToken;
use Carbon\Carbon;

class ApiAuth
{
    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest($request)
    {
        $token = $request->bearerToken();

        return $token;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$token = $this->getTokenForRequest($request)) {
            return response()->json([
                'errors' => [
                    'type'      => 'token',
                    'message'   => 'Missing token'
                ]
            ], 400);
        }

        if (empty($request->input('device_id'))) {
            return response()->json([
                'errors' => [
                    'type'      => 'device_id',
                    'message'   => 'Missing device_id'
                ]
            ], 400);
        }


        $apiToken = ApiToken::where('device_id', $request->input('device_id'))->where('api_token', $token)->first();
        if (empty($apiToken->user)) {
            return response()->json([
                'errors' => [
                    'type'      => 'credentials',
                    'message'   => 'Invalid credentials'
                ]
            ], 401);
        }

        $apiToken->used_on = Carbon::now();
        $apiToken->save();

        return $next($request);
    }
}
