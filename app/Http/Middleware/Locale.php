<?php

namespace App\Http\Middleware;

use Closure;

class Locale
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
        $supported_locales = array_keys(config('app.supported_locales'));

        $user = null;
        $language = 'en';
        if ($request->expectsJson() && !empty(\Auth::guard('api')->user())) {
            $user = \Auth::guard('api')->user()->user;

            $language = $user->language;
        } else {
            if (\Auth::check()) {
                $user = \Auth::user();
                $language = $user->language;
            } else {
                $user_locales = $request->getLanguages();
                if(!empty($user_locales)) {
                    foreach ($user_locales as $lang) {
                        $lang = explode('_', $lang);
                        if(in_array($lang[0], $supported_locales)) {
                            $language = $lang[0];
                            break;
                        }
                    }
                }
            }
        }

        app('translator')->setLocale($language);

        return $next($request);
    }
}
