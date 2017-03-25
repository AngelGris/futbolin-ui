<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        $vars['title'] = 'Entrar al vestuario';
        $vars['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $vars['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        return view('auth.passwords.email', $vars);
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $vars['title'] = 'Entrar al vestuario';
        $vars['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $vars['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        /*$vars['token'] = $token;
        $vars['email'] = $request->email;*/

        return view('auth.passwords.reset', $vars);
    }
}
