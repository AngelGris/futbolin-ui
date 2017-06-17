<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Team;
use App\Matches;

class HomeController extends Controller
{
    public function showIndex()
    {
        $last_users_stats = [
            'day' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 86400))->count(),
            'week' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 604800))->count(),
            'month' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 2592000))->count(),
            'semester' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 15552000))->count(),
            'year' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 31536000))->count(),
            'total' => User::where('id', '>', 1)->count(),
        ];
        $vars = [
            'last_users' => User::where('id', '>', 1)->whereNotNull('last_activity')->orderBy('last_activity', 'DESC')->limit(10)->get(),
            'last_users_stats' => $last_users_stats,
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
