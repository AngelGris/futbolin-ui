<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Team;

class TeamController extends Controller
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
            'icon' => 'fa fa-shield',
            'title' => Auth::user()->team->name,
            'subtitle' => 'El club de tus amores'
        ];

        return view('team.index', $vars);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!is_null(Auth::user()->team)) {
            return redirect()->route('home');
        }

        $vars['title'] = 'Crear Equipo';
        $vars['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $vars['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        return view('team.create', $vars);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * Create Team
         */
        $this->validate($request, [
            'name' => 'required|min:3|max:255',
            'short_name' => 'required|max:5',
            'stadium_name' => 'required|min:3|max:255',
            'primary_color' => 'required|size:7',
            'secondary_color' => 'required|size:7'
        ]);

        $request['text_color'] = textColor(sscanf($request['primary_color'], "#%02x%02x%02x"), sscanf($request['secondary_color'], "#%02x%02x%02x"));

        $team = Auth::user()->createTeam($request);

        /**
         * Create players
         */
        $players = [
            1   => 'ARQ',
            2   => 'DEF',
            3   => 'DEF',
            4   => 'DEF',
            5   => 'DEF',
            6   => 'MED',
            7   => 'MED',
            8   => 'MED',
            9   => 'ATA',
            10  => 'MED',
            11  => 'ATA',
            12  => 'ARQ',
            13  => 'DEF',
            14  => 'DEF',
            15  => 'MED',
            16  => 'MED',
            17  => 'MED',
            18  => 'MED',
            19  => 'ATA',
            20  => 'ARQ',
            21  => 'DEF',
        ];

        if (rand(1, 100) > 75) {
            $players[4] = 'MED';
        }
        if (rand(1, 100) > 50) {
            $players[5] = 'MED';
        }
        if (rand(1, 100) <= 25) {
            $players[6] = 'DEF';
        }
        if (rand(1, 100) > 75) {
            $players[10] = 'ATA';
        }
        if ($value = rand(1, 100) > 50) {
            if ($value <= 75) {
                $players[14] = 'MED';
            } else {
                $players[14] = 'ATA';
            }
        }
        if ($value = rand(1, 100) > 50) {
            if ($value <= 75) {
                $players[15] = 'DEF';
            } else {
                $players[15] = 'ATA';
            }
        }
        if ($value = rand(1, 100) > 33) {
            if ($value <= 66) {
                $players[17] = 'DEF';
            } else {
                $players[17] = 'ATA';
            }
        }
        if ($value = rand(1, 100) > 50) {
            if ($value <= 75) {
                $players[18] = 'DEF';
            } else {
                $players[18] = 'ATA';
            }
        }
        if ($value = rand(1, 100) > 50) {
            if ($value <= 75) {
                $players[19] = 'DEF';
            } else {
                $players[19] = 'MED';
            }
        }
        if ($value = rand(1, 100) > 40) {
            if ($value <= 60) {
                $players[21] = 'DEF';
            } elseif ($value <= 80) {
                $players[21] = 'MED';
            } else {
                $players[21] = 'ATA';
            }
        }

        foreach ($players as $num => $pos) {
            $player = $team->createPlayer($num, $pos);
        }

        $team->save();

        return redirect()->route('strategy');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $vars = [
            'icon' => 'fa fa-shield',
            'title' => Auth::user()->team->name,
            'subtitle' => 'El club de tus amores'
        ];

        return view('team.edit', $vars);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:255',
            'short_name' => 'required|max:5',
            'stadium_name' => 'required|min:3|max:255',
            'primary_color' => 'required|size:7',
            'secondary_color' => 'required|size:7'
        ]);

        $team = Auth::user()->team;
        $team->name = $request->name;
        $team->short_name = $request->short_name;
        $team->stadium_name = $request->stadium_name;
        $team->primary_color = $request->primary_color;
        $team->secondary_color = $request->secondary_color;
        $team->text_color = textColor(sscanf($request['primary_color'], "#%02x%02x%02x"), sscanf($request['secondary_color'], "#%02x%02x%02x"));
        $team->save();

        \Session::flash('flash_success', 'Equipo actualizado');

        return redirect()->route('team');
    }

    /**
     * Show strategy page
     */
    public function showStrategy()
    {
        $team = Auth::user()->team;

        $vars = [
            'icon' => 'fa fa-gears',
            'title' => 'Estratégia',
            'subtitle' => 'Los engranajes de la máquina',
            'strategy' => $team->strategy->id,
            'formation' => $team->formation,
        ];

        $strategies = DB::table('strategies')->orderBy('name')->get();

        foreach ($team->players as $player) {
            $vars['players'][$player['id']] = $player;
        }

        foreach ($strategies as $strategy) {
            $vars['strategies'][$strategy->id]['id'] = $strategy->id;
            $vars['strategies'][$strategy->id]['name'] = $strategy->name;
            for ($i = 1; $i <= 11; $i++) {
                $vars['strategies'][$strategy->id][$i]['pos'] = strtolower($strategy->{sprintf('j%02d_pos', $i)});
                $vars['strategies'][$strategy->id][$i]['left'] = (int)(100 - ($strategy->{sprintf('j%02d_start_x', $i)} / 0.9));
                $vars['strategies'][$strategy->id][$i]['top'] = (int)(100 - ($strategy->{sprintf('j%02d_start_y', $i)} / 0.6));
            }
        }

        return view('team.strategy', $vars);
    }

    /**
     * Save team formation
     */
    public function updateFormation(Request $request)
    {
        $team = Auth::user()->team;
        $team->formation = $request->formation;
        $team->save();
    }

    /**
     * Save team strategy
     */
    public function updateStrategy(Request $request)
    {
        $team = Auth::user()->team;
        $team->strategy_id = $request->strategy;
        $team->save();
    }

    /**
     * Show all teams
     */
    public function showAll()
    {
        $vars = [
            'icon' => 'fa fa-soccer-ball-o',
            'title' => 'Equipos',
            'subtitle' => 'Estos son, aquí están',
            'sparrings' => Team::where('user_id', '=', 1)->get(),
            'teams' => Team::where('user_id', '>', 1)->get(),
            'playable' => Auth::user()->team->teamPlayable
        ];

        return view('team.listing', $vars);
    }
}