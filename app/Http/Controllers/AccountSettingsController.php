<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vars = [
            'icon' => 'fa fa-user',
            'title' => __('headers.profile_edit_title'),
            'subtitle' => __('headers.profile_edit_subtitle')
        ];

        return view('accountsettings.index', $vars);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255'
        ]);

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'user'  => $user
            ], 200);
        } else {
            \Session::flash('flash_success', __('messages.profile_updated'));

            return redirect()->route('profile.edit');
        }
    }

    public function editPassword()
    {
        $vars = [
            'icon' => 'fa fa-user',
            'title' => __('headers.profile_password_edit_title'),
            'subtitle' => __('headers.profile_password_edit_subtitle')
        ];

        return view('accountsettings.password', $vars);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        if (Hash::check($request->input('old_password'), $user->password))
        {
            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            if ($request->expectsJson()) {
                return response()->json([], 204);
            } else {
                \Session::flash('flash_success', __('messages.password_updated'));

                return redirect()->route('profile.edit');
            }
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [
                        'type'      => 'old_password',
                        'message'   => 'Contraseña incorrecta'
                    ]
                ], 400);
            } else {
                return redirect()
                        ->route('profile.password')
                        ->withErrors(['old_password' => __('errors.current_password_incorrect')])
                        ->withInput($request->only('old_password', 'new_password', 'new_password_confirmation'));
            }
        }
    }
}
