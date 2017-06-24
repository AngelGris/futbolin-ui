<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Team;

class TeamController extends Controller
{
    public function index()
    {
        $vars = [
            'teams' => Team::where('user_id', '>', 1)->orderBy('last_trainning', 'DESC')->get(),
        ];

        return view('admin.team.index', $vars);
    }

    public function show($domain, \App\Team $team)
    {
        return view('admin.team.show', ['team' => $team]);
    }
}
