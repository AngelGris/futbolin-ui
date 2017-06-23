<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Team;
use App\Matches;
use App\Player;

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
            'secondary_color' => 'required|size:7',
            'shield' => 'required',
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
            18  => 'ATA',
            19  => 'DEF',
            20  => 'ARQ',
            21  => 'DEF',
        ];

        if (rand(1, 100) > 60) {
            $players[5] = 'MED';
        }
        if (rand(1, 100) <= 40) {
            $players[6] = 'DEF';
        }
        if (rand(1, 100) > 50) {
            $players[10] = 'ATA';
        }
        if ($value = rand(1, 100) > 50) {
            $players[19] = 'ATA';
        }
        if ($value = rand(1, 100) > 40) {
            if ($value <= 60) {
                $player[20] = 'DEF';
            } elseif ($value <= 80) {
                $player[20] = 'MED';
            } else {
                $player[20] = 'ATA';
            }
        }
        if ($value = rand(1, 100) > 50) {
            $players[21] = 'MED';
        }

        if (!is_integer($team)) {
            foreach ($players as $num => $pos) {
                $player = $team->createPlayer($num, $pos);
            }
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
    public function show(\App\Team $team)
    {
        $matches = Matches::where(function($query) use ($team) {
            $query->where('local_id', '=', $team['id']);
            $query->orWhere('visit_id', '=', $team['id']);
        })
        ->where('type_id', '>', 1)
        ->latest()
        ->get();

        $games = [[0, 0, 0], [0, 0, 0]];
        $goals = [[0, 0], [0, 0]];
        $last_matches = [];
        $strategy = [];
        foreach ($matches as $match) {
            if (count($last_matches) < 5) {
                if (empty($last_matches)) {
                    $data = getMatchLog($match['logfile']);
                    if ($match['local_id'] == $team['id']) {
                        foreach ($data['local']['formation'] as $form) {
                            $player = Player::where('team_id', '=', $team->id)->where('number', '=', $form['number'])->first();
                            $strategy[] = [
                                'left' => $form['top'] * 1.5,
                                'top' => $form['left'],
                                'position' => (!empty($player) ? $player['position'] : ''),
                                'number' => $form['number'],
                                'retiring' => (!empty($player) ? $player['retiring'] : FALSE),
                            ];
                        }
                    } else {
                        foreach ($data['visit']['formation'] as $form) {
                            $player = Player::where('team_id', '=', $team->id)->where('number', '=', $form['number'])->first();
                            $strategy[] = [
                                'left' => (100 - $form['top']) * 1.5,
                                'top' => 100 - $form['left'],
                                'position' => (!empty($player) ? $player['position'] : ''),
                                'number' => $form['number'],
                                'retiring' => (!empty($player) ? $player['retiring'] : FALSE),
                            ];
                        }
                    }
                }

                $won = FALSE;
                if (($team['id'] == $match->local_id && $match->local_goals > $match->visit_goals) || ($team['id'] == $match->visit_id && $match->local_goals < $match->visit_goals)) {
                    $won = TRUE;
                }

                $last_matches[] = [
                    'date' => date('d/m/y', strtotime($match->created_at)),
                    'local_id' => $match->local_id,
                    'local' => $match->local->short_name,
                    'local_goals' => $match->local_goals,
                    'visit_id' => $match->visit_id,
                    'visit' => $match->visit->short_name,
                    'visit_goals' => $match->visit_goals,
                    'log_file' => $match->logfile,
                    'won' => $won,
                ];
            }

            $pos = 0;
            if ($match->local_id != $team['id'])
            {
                $pos = 1;
            }

            $games[$pos][$match->winner]++;
            $goals[$pos][0] += $match->local_goals;
            $goals[$pos][1] += $match->visit_goals;
        }

        $team_id = Auth::user()->team->id;

        $matches = Matches::whereIn('local_id', [$team_id, $team['id']])
            ->WhereIn('visit_id', [$team_id, $team['id']])
            ->latest()
            ->get();

        $games_versus = [[0, 0, 0], [0, 0, 0]];
        $goals_versus = [[0, 0], [0, 0]];
        $last_matches_versus = [];

        foreach ($matches as $match)
        {
            $pos = 0;
            if ($match->local_id != $team['id'])
            {
                $pos = 1;
            }

            $games_versus[$pos][$match->winner]++;
            $goals_versus[$pos][0] += $match->local_goals;
            $goals_versus[$pos][1] += $match->visit_goals;

            if (count($last_matches_versus) < 5)
            {
                $won = FALSE;
                if (($team['id'] == $match->local_id && $match->local_goals > $match->visit_goals) || ($team['id'] == $match->visit_id && $match->local_goals < $match->visit_goals)) {
                    $won = TRUE;
                }

                $last_matches_versus[] = [
                    'date' => date('d/m/y', strtotime($match->created_at)),
                    'local_id' => $match->local_id,
                    'local' => $match->local->short_name,
                    'local_goals' => $match->local_goals,
                    'visit_id' => $match->visit_id,
                    'visit' => $match->visit->short_name,
                    'visit_goals' => $match->visit_goals,
                    'log_file' => $match->logfile,
                    'won' => $won,
                ];
            }
        }

        $vars = [
            'icon' => 'fa fa-shield',
            'title' => $team->name,
            'subtitle' => 'Estudiando al rival',
            'team' => $team,
            'matches' => $games,
            'goals' => $goals,
            'last_matches' => $last_matches,
            'strategy' => $strategy,
            'matches_versus' => $games_versus,
            'goals_versus' => $goals_versus,
            'last_matches_versus' => $last_matches_versus,
        ];
        return view('team.show', $vars);
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
            'secondary_color' => 'required|size:7',
            'shield' => 'required',
        ]);

        $team = Auth::user()->team;
        $team->name = $request->name;
        $team->short_name = $request->short_name;
        $team->stadium_name = $request->stadium_name;
        $team->primary_color = $request->primary_color;
        $team->secondary_color = $request->secondary_color;
        $team->text_color = textColor(sscanf($request['primary_color'], "#%02x%02x%02x"), sscanf($request['secondary_color'], "#%02x%02x%02x"));
        $team->shield = $request->shield;
        $team->save();

        \Session::flash('flash_success', 'Equipo actualizado');

        return redirect()->route('team');
    }

    /**
     * Show versus statistics
     */
    public function showStatsVersus($rival)
    {
        $team_id = Auth::user()->team->id;

        $matches = Matches::whereIn('local_id', [$team_id, $rival])
            ->WhereIn('visit_id', [$team_id, $rival])
            ->latest()
            ->get();

        $games = [[0, 0, 0], [0, 0, 0]];
        $goals = [[0, 0], [0, 0]];
        $last_matches = [];

        foreach ($matches as $match)
        {
            $pos = 0;
            if ($match->local_id != $team_id)
            {
                $pos = 1;
            }

            $games[$pos][$match->winner]++;
            $goals[$pos][0] += $match->local_goals;
            $goals[$pos][1] += $match->visit_goals;

            if (count($last_matches) < 5)
            {
                $score = $match->local_goals . '-' . $match->visit_goals;
                if (($pos == 0 && $match->winner == 1) || ($pos == 1 && $match->winner == 2))
                {
                    $score = '<strong>' . $score . '</strong>';
                }

                $last_matches[] = [
                    'date' => date('d/m/Y', strtotime($match->created_at)),
                    'condition' => ($pos == 0 ? 'Local' : 'Visitante'),
                    'score' => $score,
                    'logfile' => $match->logfile,
                ];
            }
        }

        $vars = [
            'rival' => Team::find($rival),
            'matches' => $games,
            'goals' => $goals,
            'last_matches' => $last_matches,
        ];

        return view('team.stats.versus', $vars);
    }

    /**
     * Show strategy page
     */
    public function showStrategy()
    {
        $team = Auth::user()->team;

        $vars = [
            'icon' => 'fa fa-gears',
            'title' => 'Estrategia',
            'subtitle' => 'Aceitando las piezas',
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

    public function train(Request $request)
    {
        $team = Auth::user()->team;
        if ($team->trainable) {
            if (is_null($team->last_trainning) || $team->last_trainning->timestamp < $_SERVER['REQUEST_TIME'] - \Config::get('constants.TIME_TO_TRAIN') - \Config::get('constants.TRAIN_TIME_SPAM')) {
                $team->trainning_count = 1;
            } else {
                $team->trainning_count++;
            }
            $trainning_points = \Config::get('constants.TRAINNING_POINTS') * min(5, $team->trainning_count);

            foreach ($team->players as $player) {
                $player->experience += $trainning_points;
                if ($player->experience >= 100) {
                    $player->upgrade();
                } else {
                    $player->save();
                }
            }

            $team->last_trainning = $_SERVER['REQUEST_TIME'];
            $team->save();

            if ($team->trainning_count < 5) {
                $message = '<p>Es tu primer entrenamiento y tus jugadores ganaron ' . $trainning_points . ' puntos de experiencia.</p>';
                $message .= '<p>Vuelve a entrenar mañana para ganar ' . ($trainning_points + \Config::get('constants.TRAINNING_POINTS')) . ' puntos mas</p>';
            } else {
                $message = '<p>Entrenaste a tu equipo ' . $team->trainning_count . ' días seguidos y tus jugadores ganaron ' . $trainning_points . ' puntos de experiencia.</p>';
                $message .= '<p>Vuelve a entrenar mañana para ganar ' . $trainning_points . ' puntos mas</p>';
            }
            return json_encode(['title' => 'Entrenamiento', 'message' => $message, 'remaining' => $team->trainable_remaining]);
        } else {
            return json_encode(['title' => 'Entrenamiento', 'message' => '<p>Debes esperar ' . readableTime($team->trainable_remaining) . ' para poder entrenar nuevamente.</p>', 'remaining' => $team->trainable_remaining]);
        }
    }

    /**
     * Save team formation
     */
    public function updateFormation(Request $request)
    {
        $team = Auth::user()->team;
        $formation = [];
        foreach ($request->formation as $value) {
            if (in_array($value, $formation)) {
                $formation[] = '0';
            } else {
                $formation[] = $value;
            }
        }
        $team->formation = $formation;
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
    public function showAll(Request $request)
    {
        $user = Auth::user();
        $matches = Matches::where('local_id', '=', $user->team->id)->where('type_id', '=', 2)->where('created_at', '>', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - 86400))->get();

        $played = [];
        foreach ($matches as $match) {
            $played[$match['visit_id']] = readableTime(86400 - ($_SERVER['REQUEST_TIME'] - strtotime($match['created_at'])), TRUE);
        }

        $vars = [
            'icon' => 'fa fa-handshake-o',
            'title' => 'Amistosos',
            'subtitle' => 'Hora de ponernos a prueba',
            'sparrings' => Team::where('user_id', '=', 1)->orderBy('name')->get(),
            'teams' => Team::where('user_id', '>', 1)->get(),
            'played' => $played,
            'playable' => $user->team->playable,
        ];

        return view('team.listing', $vars);
    }
}