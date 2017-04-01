<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            'icon' => 'iconfa-shield',
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
            'stadium_name' => 'required|min:3|max:255',
            'primary_color' => 'required|size:7',
            'secondary_color' => 'required|size:7'
        ]);

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
            9   => 'MED',
            10  => 'ATA',
            11  => 'ATA',
            12  => 'ARQ',
            13  => 'DEF',
            14  => 'DEF',
            15  => 'DEF',
            16  => 'MED',
            17  => 'MED',
            18  => 'ATA',
            19  => 'ATA',
            20  => 'ARQ',
        ];

        $formation = [];
        foreach ($players as $num => $pos) {
            $player = $team->createPlayer($num, $pos);
            $formation[] = $player->id;
        }
        $formation = array_slice($formation, 0, 18);

        $team->formation = $formation;
        $team->save();

        return redirect()->route('home');
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
            'icon' => 'iconfa-shield',
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
            'stadium_name' => 'required|min:3|max:255',
            'primary_color' => 'required|size:7',
            'secondary_color' => 'required|size:7'
        ]);

        $team = Auth::user()->team;
        $team->name = $request->name;
        $team->stadium_name = $request->stadium_name;
        $team->primary_color = $request->primary_color;
        $team->secondary_color = $request->secondary_color;
        $team->save();

        \Session::flash('flash_success', 'Equipo actualizado');

        return redirect()->route('team');
    }

    /**
     * Show strategy page
     */
    public function showStrategy()
    {
        $vars = [
            'icon' => 'iconfa-beaker',
            'title' => 'Estratégia',
            'subtitle' => 'El laboratorio del fútbol',
            'players' => Auth::user()->team->players,
            'strategy' => Auth::user()->team->strategy->id,
        ];

        $strategies = DB::table('strategies')->orderBy('name')->get();

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
}