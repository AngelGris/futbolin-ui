<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Team;
use App\Matches;

class HomeController extends Controller
{
    public function showIndex()
    {
        $vars = [
            'last_teams' => Team::where('user_id', '>', 1)->latest()->limit(10)->get(),
            'last_matches' => Matches::latest()->limit(10)->get(),
        ];
        return view('admin.home', $vars);
    }

    public function editPassword()
    {
        return view('admin.password');
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

            return redirect()->route('admin', ['domain' => getDomain()]);
        } else {
            return redirect()
                    ->route('profile.password')
                    ->withErrors(['old_password' => 'Contraseña Actual incorrecta'])
                    ->withInput($request->only('old_password', 'new_password', 'new_password_confirmation'));
        }
    }
}
