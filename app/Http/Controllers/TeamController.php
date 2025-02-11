<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\TournamentCategory;
use App\Matches;
use App\Player;
use App\Strategy;
use App\Team;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vars = [
            'icon' => 'fa fa-shield',
            'title' => __('headers.team_title', ['team_name' => Auth::user()->team->name]),
            'subtitle' => __('headers.team_subtitle')
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Team $team, Request $request)
    {
        $matches = Matches::where(function($query) use ($team) {
            $query->where('local_id', '=', $team->id);
            $query->orWhere('visit_id', '=', $team->id);
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
                $won = FALSE;
                if (($team['id'] == $match->local_id && $match->local_goals > $match->visit_goals) || ($team['id'] == $match->visit_id && $match->local_goals < $match->visit_goals)) {
                    $won = TRUE;
                }

                $last_matches[] = [
                    'date' => $match->created_at->format('d/m/y'),
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

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        if (is_null($user->team)) {
            $team_id = 0;
        } else {
            $team_id = $user->team->id;
        }

        // Last friendly match
        $friendly = Matches::where('local_id', '=', $user->team->id)->where('visit_id', '=', $team->id)->where('type_id', '=', 2)->where('created_at', '>', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - 86400))->orderBy('created_at', 'DESC')->first();

        if ($friendly) {
            $next_friendly = readableTime(86400 - ($_SERVER['REQUEST_TIME'] - strtotime($friendly['created_at'])), TRUE);
        } else {
            $next_friendly = null;
        }

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

        if($request->expectsJson()) {
            return response()->json([
                'team'                  => $team,
                'strategy'              => $team->strategy_public,
                'playable'              => ($user->team->playable && $team->playable && $team->id != $user->team->id),
                'next_friendly'         => $next_friendly,
                'matches'               => [
                    'local' => [
                        'tied'              => $games[0][0],
                        'won'               => $games[0][1],
                        'lost'              => $games[0][2],
                        'total'             => $games[0][0] + $games[0][1] + $games[0][2],
                        'goals_favor'       => $goals[0][0],
                        'goals_against'     => $goals[0][1],
                        'goals_difference'  => $goals[0][0] - $goals[0][1]
                    ],
                    'visit' => [
                        'tied'              => $games[1][0],
                        'won'               => $games[1][2],
                        'lost'              => $games[1][1],
                        'total'             => $games[1][0] + $games[1][1] + $games[1][2],
                        'goals_favor'       => $goals[1][1],
                        'goals_against'     => $goals[1][0],
                        'goals_difference'  => $goals[1][1] - $goals[1][0]
                    ]
                ],
                'last_matches'          => $last_matches,
                'matches_versus'        => [
                    'local' => [
                        'tied'              => $games_versus[0][0],
                        'won'               => $games_versus[0][1],
                        'lost'              => $games_versus[0][2],
                        'total'             => $games_versus[0][0] + $games_versus[0][1] + $games_versus[0][2],
                        'goals_favor'       => $goals_versus[0][0],
                        'goals_against'     => $goals_versus[0][1],
                        'goals_difference'  => $goals_versus[0][0] - $goals_versus[0][1]
                    ],
                    'visit' => [
                        'tied'              => $games_versus[1][0],
                        'won'               => $games_versus[1][2],
                        'lost'              => $games_versus[1][1],
                        'total'             => $games_versus[1][0] + $games_versus[1][1] + $games_versus[1][2],
                        'goals_favor'       => $goals_versus[1][1],
                        'goals_against'     => $goals_versus[1][0],
                        'goals_difference'  => $goals_versus[1][1] - $goals_versus[1][0]
                    ]
                ],
                'last_matches_versus'   => $last_matches_versus,
                'trophies'              => $team->trophies,
            ], 200);
        } else {
            $vars = [
                'icon'                  => 'fa fa-shield',
                'title'                 => __('headers.other_team_title', ['team_name' => $team->name]),
                'subtitle'              => __('headers.other_team_subtitle'),
                'team'                  => $team,
                'playable'              => ($user->team->playable && $team->playable && $team->id != $user->team->id),
                'next_friendly'         => $next_friendly,
                'matches'               => $games,
                'goals'                 => $goals,
                'last_matches'          => $last_matches,
                'strategy'              => $team->strategy_public,
                'matches_versus'        => $games_versus,
                'goals_versus'          => $goals_versus,
                'last_matches_versus'   => $last_matches_versus,
            ];
            return view('team.show', $vars);
        }
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
            'title' => __('headers.team_title', ['team_name' => Auth::user()->team->name]),
            'subtitle' => __('headers.team_subtitle')
        ];

        return view('team.edit', $vars);
    }

    /**
     * Show all teams
     */
    public function showAll(Request $request)
    {
        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }
        $matches = Matches::where('local_id', '=', $user->team->id)->where('type_id', '=', 2)->where('created_at', '>', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - 86400))->get();

        $played = [];
        foreach ($matches as $match) {
            $played[$match['visit_id']] = readableTime(86400 - ($_SERVER['REQUEST_TIME'] - strtotime($match['created_at'])), TRUE);
        }

        $teams = Team::where('user_id', '>', 1)->orderBy('name')->get();
        foreach ($teams as &$team) {
            $team->user_name = $team->user->name;

            if (empty($played[$team->id])) {
                $team->played = NULL;
            } else {
                $team->played = $played[$team->id];
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'sparrings' => Team::where('user_id', '=', 1)->orderBy('name')->get(),
                'teams'     => $teams,
            ], 200);
        } else {
            $vars = [
                'icon' => 'fa fa-handshake-o',
                'title' => __('headers.friendlies_title'),
                'subtitle' => __('headers.friendlies_subtitle'),
                'sparrings' => Team::where('user_id', '=', 1)->orderBy('name')->get(),
                'teams' => $teams,
                'playable' => $user->team->playable,
            ];

            return view('team.listing', $vars);
        }
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
                    'condition' => __($pos == 0 ? 'labels.home' : 'labels.away'),
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
            'title' => __('headers.strategy_title'),
            'subtitle' => __('headers.strategy_subtitle'),
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
            'name'              => 'required|min:3|max:255',
            'short_name'        => 'required|max:5',
            'stadium_name'      => 'required|min:3|max:255',
            'primary_color'     => 'required|size:7',
            'secondary_color'   => 'required|size:7',
            'shield'            => 'required',
        ]);

        $request['text_color'] = textColor(sscanf($request['primary_color'], "#%02x%02x%02x"), sscanf($request['secondary_color'], "#%02x%02x%02x"));

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
            if (!is_null($user->team)) {
                return response()->json([
                    'errors' => [
                        'type'      => 'team_exists',
                        'message'   => 'El usuario ya tiene un equipo'
                    ]
                ], 400);
            }
        } else {
            $user = Auth::user();
        }
        $team = $user->createTeam($request);

        if (!is_integer($team)) {
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

            $formation = [];
            foreach ($players as $num => $pos) {
                $player = $team->createPlayer($num, $pos);
                if (count($formation) < 18) {
                    $formation[] = $player->id;
                }
            }
            $team->formation = $formation;

            $team->save();

            // Check if the team is ready to play
            $team->updatePlayable();
        } else {
            $team = $user->team;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'team' => $team
            ], 201);
        } else {
            $request->session()->flash('show_walkthrough', TRUE);

            return redirect()->route('strategy');
        }
    }

    public function train(Request $request)
    {
        $api = FALSE;
        if ($request->expectsJson()) {
            if (empty(Auth::guard('api')->user())) {
                $team = Auth::user()->team;
            } else {
                $team = Auth::guard('api')->user()->user->team;
                $api = TRUE;
            }
        } else {
            $team = Auth::user()->team;
        }

        $restart = !empty($request->input('restart')) && filter_var($request->input('restart'), FILTER_VALIDATE_BOOLEAN);

        /**
         * Train if the team is in trainning spam or it's to restart
         * Else ask user to restart or keep trainnings run
         */
        if ($team->trainning_count == 0 || $team->inTrainningSpam || $restart) {
            if ($team->train()) {
                $training_points = \Config::get('constants.TRAINNING_POINTS') * min(5, $team->trainning_count);

                if ($team->trainning_count < 5) {
                    switch ($team->trainning_count) {
                        case 1:
                            $ordinal = __('ordinals.first');
                            break;
                        case 2:
                            $ordinal = __('ordinals.second');
                            break;
                        case 3:
                            $ordinal = __('ordinals.third');
                            break;
                        default:
                            $ordinal = __('ordinals.fourth');
                            break;
                    }
                    $message = '<p>' . __('messages.its_your_ordinal_training', ['ordinal' => $ordinal]) . '</p>';
                    $message .= '<p>' . __('messages.today_your_players_gained_x_points', ['training_points' => $training_points]) . '</p>';
                    $message .= '<p>' . __('messages.train_again_tomorrow_to_earn_more', ['points' => ($training_points + \Config::get('constants.TRAINNING_POINTS'))]) . '</p>';
                } else {
                    $message = '<p>' . __('messages.you_trained_your_team_days_in_a_row', ['training_count' => $team->trainning_count]) . '</p>';
                    $message .= '<p>' . __('messages.today_your_players_gained_x_points', ['training_points' => $training_points]) . '</p>';
                    $message .= '<p>' . __('messages.train_again_tomorrow_to_earn_more', ['points' => $training_points]) . '</p>';
                }

                if ($api) {
                    return response()->json([
                        'trained'       => TRUE,
                        'count'         => $team->trainning_count,
                        'points'        => $training_points,
                        'next'          => $team->trainable_remaining,
                        'show_options'  => FALSE
                    ], 200);
                } else {
                    return response()->json([
                        'title'     => __('labels.training'),
                        'message'   => $message,
                        'remaining' => $team->trainable_remaining
                    ], 200);
                }
            } else {
                if ($api) {
                    return response()->json([
                        'trained'       => FALSE,
                        'count'         => $team->trainning_count,
                        'points'        => 0,
                        'next'          => $team->trainable_remaining,
                        'show_options'  => FALSE
                    ], 200);
                } else {
                    return response()->json([
                        'title'     => __('labels.training'),
                        'message'   => '<p>' . __('messages.must_wait_to_train', ['time_remaining' => readableTime($team->trainable_remaining)]) . '</p>',
                        'remaining' => $team->trainable_remaining
                    ], 200);
                }
            }
        } else {
            if ($api) {
                return response()->json([
                    'trained'       => FALSE,
                    'count'         => $team->trainning_count,
                    'points'        => 0,
                    'next'          => $team->trainable_remaining,
                    'show_options'  => TRUE
                ], 200);
            } else {
                return response()->json([
                    'title'     => __('labels.training'),
                    'message'   => '<p>' . __('messages.training_streak_lost', ['training_count' => $team->trainning_count]) . '</p><button id="btn-trainning-keep" data-token="' . csrf_token() . '" class="btn btn-sm btn-primary" style="margin-right:10px;">' . __('labels.keep_training_streak') . '</button><button id="btn-trainning-restart" data-token="' . csrf_token() . '" class="btn btn-sm btn-default">' . __('labels.start_new_streak') . '</button>',
                    'remaining' => 0,
                ]);
            }
        }
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

        if ($request->expectsJson()) {
            $user = Auth::guard('api')->user()->user;
        } else {
            $user = Auth::user();
        }

        $team = $user->team;
        $team->name = $request->name;
        $team->short_name = $request->short_name;
        $team->stadium_name = $request->stadium_name;
        $team->primary_color = $request->primary_color;
        $team->secondary_color = $request->secondary_color;
        $team->text_color = textColor(sscanf($request['primary_color'], "#%02x%02x%02x"), sscanf($request['secondary_color'], "#%02x%02x%02x"));
        $team->shield = $request->shield;
        $team->save();

        if ($request->expectsJson()) {
            return response()->json([
                'team'  => $team
            ], 200);
        } else {
            \Session::flash('flash_success', __('messages.team_updated'));

            return redirect()->route('team');
        }
    }

    /**
     * Save team formation
     */
    public function updateFormation(Request $request)
    {
        $this->validate($request, [
            'formation' => 'required|array|size:18'
        ]);

        if ($request->expectsJson()) {
            if (empty(Auth::guard('api')->user())) {
                $team = Auth::user()->team;
            } else {
                $team = Auth::guard('api')->user()->user->team;
            }
        } else {
            $team = Auth::user()->team;
        }
        $formation = [];
        $players = [];
        $result = Player::whereIn('id', $request->input('formation'))->where('team_id', $team->id)->get();
        foreach ($result as $row) {
            $players[] = $row->id;
        }

        foreach ($request->formation as $value) {
            if (!in_array($value, $players) || in_array($value, $formation)) {
                $formation[] = '0';
            } else {
                $formation[] = $value;
            }
        }
        $team->formation = $formation;
        $team->save();
        $team->updatePlayable();

        $team->append(['formation_objects']);

        if ($request->expectsJson()) {
            return response()->json([
                'team' => $team
            ], 200);
        }
    }

    /**
     * Update players numbers
     */
    public function updateNumbers(Request $request)
    {
        $api = FALSE;
        if ($request->expectsJson()) {
            if (empty(Auth::guard('api')->user())) {
                $team = Auth::user()->team;
            } else {
                $team = Auth::guard('api')->user()->user->team;
                $api = TRUE;
            }
        } else {
            $team = Auth::user()->team;
        }

        $count = 0;
        foreach ($team->players as $player) {
            $index = array_search($player->id, $team->formation);
            if ($index === FALSE) {
                $number = 19 + $count;
                $count++;
            } else {
                $number = $index + 1;
            }
            $player->number = $number;
            $player->save();
        }

        if ($api) {
            return response()->json([
                'team' => $team
            ], 200);
        } else {
            return redirect()->route('strategy');
        }
    }

    /**
     * Save team strategy
     */
    public function updateStrategy(Request $request)
    {
        $this->validate($request, [
            'strategy' => 'required|integer|exists:strategies,id'
        ]);

        $api = FALSE;
        if ($request->expectsJson()) {
            if (empty(Auth::guard('api')->user())) {
                $team = Auth::user()->team;
            } else {
                $team = Auth::guard('api')->user()->user->team;
                $api = TRUE;
            }
        } else {
            $team = Auth::user()->team;
        }
        $team->strategy_id = $request->strategy;
        $team->save();

        $team->append(['formation_objects']);

        if ($api) {
            return response()->json([
                'team' => $team
            ], 200);
        }
    }
}