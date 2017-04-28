<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountSettingsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vars = [
            'icon' => 'fa fa-user',
            'title' => 'Editar Perfil',
            'subtitle' => 'Dime quién eres'
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

        $user = Auth::user();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->save();

        \Session::flash('flash_success', 'Perfil actualizado');

        return redirect()->route('profile.edit');
    }

    public function editPassword()
    {
        $vars = [
            'icon' => 'fa fa-user',
            'title' => 'Cambiar Contraseña',
            'subtitle' => 'La palabrita mágica'
        ];

        return view('accountsettings.password', $vars);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();
        if (Hash::check($request->input('old_password'), $user->password))
        {
            $user->password = Hash::make($request->input('new_password'));
            $user->save();

            \Session::flash('flash_success', 'Contraseña actualizada');

            return redirect()->route('profile.edit');
        } else {
            return redirect()
                    ->route('profile.password')
                    ->withErrors(['old_password' => 'Contraseña Actual incorrecta'])
                    ->withInput($request->only('old_password', 'new_password', 'new_password_confirmation'));
        }
    }
}
