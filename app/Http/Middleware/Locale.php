<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;

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
            if (\Auth::check()) { // Get language from user profile
                $user = \Auth::user();
                $language = $user->language;
            } elseif (
                $request->get('language', null) &&
                in_array($request->get('language'), $supported_locales)
            ) { // Get language from request for when the user select a different language
                $language = $request->get('language');
            } elseif (
                Cookie::has('language') &&
                in_array(Cookie::get('language'), $supported_locales)
            ) { // Get language from cookie
                $language = Cookie::get('language');
            } else { // Get default language from browser
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

        Cookie::queue('language', $language);
        app('translator')->setLocale($language);

        return $next($request);
    }
}
