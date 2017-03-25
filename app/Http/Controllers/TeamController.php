<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        //
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

        $params['title'] = 'Crear Equipo';
        $params['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $params['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        return view('team.create', $params);
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

        foreach ($players as $num => $pos) {
            $team->createPlayer($num, $pos);
        }

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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
