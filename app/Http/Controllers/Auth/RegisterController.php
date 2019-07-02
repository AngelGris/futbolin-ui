<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/equipo/crear';

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
     * Show the application registration form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $params['title'] = config('app.name') . ' - ' . __('labels.affiliate');
        $params['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $params['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        $params['supported_languages'] = config('app.supported_locales');
        $params['current_language'] = $request->old('language') ? $request->old('language') : app('translator')->getLocale();

        app('translator')->setLocale($params['current_language']);

        return view('auth.register', $params);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name'    => 'required|max:255',
            'last_name'     => 'required|max:255',
            'email'         => 'required|email|max:255|unique:users',
            'language'      => 'required|max:2',
            'password'      => 'required|min:6|confirmed',
        ]);
    }

    /**
     * API register
     *
     * @param \Illuminate\Http\Request $request
     * @return User
     */
    public function apiRegister(Request $request)
    {
        $this->validate($request, [
            'first_name'    => 'required|max:255',
            'last_name'     => 'required|max:255',
            'email'         => 'required|email|max:255|unique:users',
            'language'      => 'required|max:2',
            'password'      => 'required|min:6|confirmed',
            'device_id'     => 'required|string',
            'device_name'   => 'required|string'
        ]);

        // Create new user
        $user = $this->create($request->all());
        // Login in the system
        Auth::loginUsingId($user->id);
        // Generate token
        $token = $user->generateToken($request->input('device_id'), $request->input('device_name'));

        return response()->json([
            'first_name'    => $user->first_name,
            'last_name'     => $user->last_name,
            'email'         => $user->email,
            'language'      => $user->language,
            'token'         => $token
        ], 201);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'language'      => $data['language'],
            'password'      => bcrypt($data['password']),
        ]);
    }
}
