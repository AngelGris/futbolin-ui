<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/vestuario';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Login through the API
     *
     * @param Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function apiLogin(Request $request)
    {
        $this->validate($request, [
            'email'                     => 'required|email|max:255',
            'password'                  => 'required|min:6',
            'device_id'                 => 'required|string',
            'device_name'               => 'required|string',
            'push_notification_token'   => 'required|string',
        ]);

        if ($this->attemptLogin($request)) {
            $user = $this->guard()->user();
            $token = $user->generateToken($request->input('device_id'), $request->input('device_name'), $request->input('push_notification_token'));

            return response()->json([
                'token'     => $token
            ], 200);
        }

        return response()->json([
            'errors'   => [
                'type'      => 'login',
                'message'   => 'Login failed'
            ],
        ], 400);
    }

    /**
     * Logout thriugh the API
     */
    public function apiLogout(Request $request)
    {
        Auth::guard('api')->user()->delete();

        return response()->json([], 204);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $params['title'] = __('labels.enter_locker_room');
        $params['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $params['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        $params['supported_languages'] = config('app.supported_locales');
        $params['current_language'] = app('translator')->getLocale();

        return view('auth.login', $params);
    }
}