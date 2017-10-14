<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Team;
use App\Matches;
use App\Player;
use DB;

class HomeController extends Controller
{
    public function showIndex()
    {
        $last_users_stats = [
            'day' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 86400))->count(),
            'days' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 172800))->count(),
            'week' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 604800))->count(),
            'month' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 2592000))->count(),
            'semester' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 15552000))->count(),
            'year' => User::where('id', '>', 1)->where('last_activity', '>', date('Y-m-d H:i:s', time() - 31536000))->count(),
            'total' => User::where('id', '>', 1)->count(),
        ];
        $last_trainnings_stats = [
            'day' => Team::where('user_id', '>', 1)->where('last_trainning', '>', date('Y-m-d H:i:s', time() - 86400))->count(),
            'days' => Team::where('user_id', '>', 1)->where('last_trainning', '>', date('Y-m-d H:i:s', time() - 172800))->count(),
            'week' => Team::where('user_id', '>', 1)->where('last_trainning', '>', date('Y-m-d H:i:s', time() - 604800))->count(),
            'month' => Team::where('user_id', '>', 1)->where('last_trainning', '>', date('Y-m-d H:i:s', time() - 2592000))->count(),
            'semester' => Team::where('user_id', '>', 1)->where('last_trainning', '>', date('Y-m-d H:i:s', time() - 15552000))->count(),
            'year' => Team::where('user_id', '>', 1)->where('last_trainning', '>', date('Y-m-d H:i:s', time() - 31536000))->count(),
            'total' => Team::where('user_id', '>', 1)->count(),
        ];

        $cards_count = DB::table('player_cards')
            ->select(DB::raw('`cards`, COUNT(*) AS `cards_count`'))
            ->where('cards', '>', 0)
            ->groupBy('cards')
            ->orderBy('cards', 'DESC')
            ->get();

        $suspensions_count = DB::table('player_cards')
            ->join('suspensions', 'suspensions.id', '=', 'player_cards.suspension_id')
            ->select(DB::raw('`suspensions`.`name`, COUNT(*) AS `suspensions_count`'))
            ->groupBy('suspension_id')
            ->orderBy('suspensions_count', 'DESC')
            ->get();

        $injury_types = DB::table('injuries')
            ->join('players', 'players.injury_id', '=', 'injuries.id')
            ->select(DB::raw('`injuries`.`name`, COUNT(*) AS `injuries_count`'))
            ->groupBy('injury_id')
            ->orderBy('injuries_count', 'DESC')
            ->get();

        $players_energy = DB::table('players')
            ->select(DB::raw('`stamina`, COUNT(*) AS `count`'))
            ->where('team_id', '>', 1)
            ->groupBy('stamina')
            ->get();
        $aux = [];
        for ($i = 0; $i <= 100; $i += 5) {
            $aux[$i] = 0;
        }
        foreach ($players_energy as $value) {
            $index = ((int)($value->stamina / 5) *5);
            $aux[$index] += $value->count;
        }
        $players_energy = [];
        foreach ($aux as $k => $v) {
            $players_energy[] = [$k, $v];
        }

        $teams_energy = DB::table('players')
            ->select(DB::raw('`team_id`, ROUND(AVG(`stamina`)) AS `stamina`'))
            ->where('team_id', '>', 1)
            ->groupBy('team_id')
            ->get();
        $aux = [];
        for ($i = 0; $i <= 100; $i += 5) {
            $aux[$i] = 0;
        }
        foreach ($teams_energy as $value) {
            $index = ((int)($value->stamina / 5) *5);
            $aux[$index]++;
        }
        $teams_energy = [];
        foreach ($aux as $k => $v) {
            $teams_energy[] = [$k, $v];
        }

        $vars = [
            'last_users' => User::where('id', '>', 1)->whereNotNull('last_activity')->orderBy('last_activity', 'DESC')->limit(10)->get(),
            'last_users_stats' => $last_users_stats,
            'last_trainnings' => Team::where('user_id', '>', 1)->whereNotNull('last_trainning')->orderBy('last_trainning', 'DESC')->limit(10)->get(),
            'last_trainnings_stats' => $last_trainnings_stats,
            'last_teams' => Team::where('user_id', '>', 1)->latest()->limit(10)->get(),
            'last_matches' => Matches::latest()->limit(10)->get(),
            'cards_count' => $cards_count,
            'suspensions' => $suspensions_count,
            'injured_players' => Player::where('recovery', '>', 0)->orderBy('recovery')->limit(10)->get(),
            'injury_types' => $injury_types,
            'players_energy' => json_encode($players_energy),
            'teams_energy' => json_encode($teams_energy),
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
