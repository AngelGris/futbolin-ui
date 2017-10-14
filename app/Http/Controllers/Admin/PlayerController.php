<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Player;
use DB;
use PDO;

class PlayerController extends Controller
{
    public function index()
    {
    }

    public function cards()
    {
        $cards = DB::table('player_cards')
                    ->select('player_id')
                    ->where('cards', '>', 0)
                    ->orderBy('cards', 'DESC')
                    ->get();
        $player_id = [];
        foreach ($cards as $card) {
            $player_id[] = $card->player_id;
        }

        return view('admin.player.cards', ['players' => Player::whereIn('id', $player_id)->orderByRaw('FIELD(`id` , ' . implode(',', $player_id) . ') ASC')->get()]);
    }

    public function injuries($domain, \App\Team $team)
    {
        return view('admin.player.injuries', ['players' => Player::where('recovery', '>', 0)->orderBy('recovery')->get()]);
    }

    public function suspensions()
    {
        $players = Player::join('player_cards', 'player_cards.player_id', '=', 'players.id')
                         ->join('suspensions', 'suspensions.id', '=', 'player_cards.suspension_id')
                         ->where('player_cards.suspension', '>', 0)
                         ->orderBy('player_cards.suspension', 'DESC')
                         ->get();

        //dd($players[0]->suspension->name);

        return view('admin.player.suspensions', ['players' => $players]);
    }
}
