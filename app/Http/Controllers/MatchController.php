<?php

namespace App\Http\Controllers;

use App\Team;
use App\Matches;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Storage;

class MatchController extends Controller
{
    /**
     * Retrieve log for live broadcast
     *
     * @param String $logfile
     * @return Json
     */
    public function getLiveLog($logfile)
    {
        $data = getMatchLog($logfile);

        $version = isset($data['version']) ? $data['version'] : '0.0';

        $local = Team::find($data['local']['id']);
        $visit = Team::find($data['visit']['id']);

        $match = Matches::where('logfile', $logfile)->first();

        $data['time'] = (time() - $match->created_at->timestamp) * 5400 / (\Config::get('constants.LIVE_MATCH_DURATION') * 60);

        $data['logfile'] = $logfile;

        // Make translations
        if ($version != '0.0') {
            foreach ($data['plays'] as &$play) {
                $play[3] = __('highlights.play_' . $play[2], $play[3]);
            }
        }

        $data['local']['id'] = $local->id;
        $data['local']['short_name'] = $local->short_name;
        $data['local']['shield_file'] = $local->shield_file;
        $data['local']['primary_color'] = $local->primary_color;
        $data['local']['rgb_primary'] = implode(', ', sscanf($local->primary_color, "#%02x%02x%02x"));
        $data['local']['secondary_color'] = $local->secondary_color;
        $data['local']['text_color'] = $local->text_color;

        $data['visit']['id'] = $visit->id;
        $data['visit']['short_name'] = $visit->short_name;
        $data['visit']['shield_file'] = $visit->shield_file;
        $data['visit']['primary_color'] = $visit->primary_color;
        $data['visit']['rgb_primary'] = implode(', ', sscanf($visit->primary_color, "#%02x%02x%02x"));
        $data['visit']['secondary_color'] = $visit->secondary_color;
        $data['visit']['text_color'] = $visit->text_color;

        return response()->json($data);
    }

    /**
     * Load match result
     *
     * @param Request $request
     */
    public function load(Request $request)
    {
        $this->validate($request, [
            'file'  => 'required|string',
        ]);

        $data = getMatchLog($request->input('file'));

        $version = isset($data['version']) ? $data['version'] : '0.0';

        $local = Team::find($data['local']['id']);
        $visit = Team::find($data['visit']['id']);

        $local_rgb_primary = sscanf($local->primary_color, "#%02x%02x%02x");
        $visit_rgb_primary = sscanf($visit->primary_color, "#%02x%02x%02x");

        $match_type = \DB::table('matches')
            ->select('type_id')
            ->where('logfile', '=', $request->file)
            ->first();

        $show_remaining = FALSE;

        $remaining_time = 86400 - ($_SERVER['REQUEST_TIME'] - $data['timestamp']);
        if (isset($request->show_remaining) && strtolower($request->show_remaining) == 'true') {
            if ($match_type->type_id > 1 && $remaining_time > 0) {
                $show_remaining = TRUE;
            }
        }

        // Make translations
        if ($version != '0.0') {
            foreach ($data['plays'] as &$play) {
                $play[3] = __('highlights.play_' . $play[2], $play[3]);
            }
        }

        if ($request->expectsJson() && !empty(Auth::guard('api')->user())) {
            $scorers = [
                'local'     => [],
                'visit'    => []
            ];

            $highlights = [];
            foreach ($data['plays'] as $play) {
                if (in_array($play[2], [1, 2, 5, 6, 7, 8, 9, 11, 12, 18, 19, 21, 22, 23, 24, 25, 26, 27, 28, 31, 32, 33, 34, 35, 36])) {
                    $highlights[] = [
                        'type'          => $play[2],
                        'minutes'       => substr($play[0], 0, 2),
                        'background'    => ($play[1] == 0) ? $local->primary_color : $visit->primary_color,
                        'color'         => ($play[1] == 0) ? $local->text_color : $visit->text_color,
                        'highlight'     => $play[3]
                    ];
                }
            }

            foreach ($data['scorers'] as $scorer) {
                if ($scorer[1] == 0) {
                    $scorers['local'][] = [
                        'minutes'   => substr($scorer[0], 0, 2),
                        'player'    => $scorer[2]
                    ];
                } else {
                    $scorers['visit'][] = [
                        'minutes'   => substr($scorer[0], 0, 2),
                        'player'    => $scorer[2]
                    ];
                }
            }

            return response()->json([
                'assistance' => empty($data['assistance']) ? 0 : $data['assistance'],
                'incomes' => empty($data['incomes']) ? 0 : $data['incomes'],
                'show_remaining'        => $show_remaining,
                'remaining'             => $remaining_time,
                'remaining_readable'    => readableTime($remaining_time),
                'datetime'              => $data['timestamp'],
                'stadium'               => $data['stadium'],
                'local' => [
                    'name' => $local->name,
                    'primary_color' => $local->primary_color,
                    'secondary_color' => $local->secondary_color,
                    'rgb_primary' => $local_rgb_primary,
                    'text_color' => $local->text_color,
                    'shield' => (int)preg_replace('/\D+/', '', $local->shieldFile),
                    'goals' => $data['local']['goals'],
                    'formation' => $data['local']['formation'],
                    'posession' => $data['local']['posessionPer'],
                    'shots' => $data['local']['shots'],
                    'shots_goal' => $data['local']['shotsOnTarget'],
                    'yellow_cards' => (isset($data['local']['yellow_cards']) ? $data['local']['yellow_cards'] : -1),
                    'red_cards' => (isset($data['local']['red_cards']) ? $data['local']['red_cards'] : -1),
                    'substitutions' => (isset($data['local']['substitutions']) ? $data['local']['substitutions'] : -1),
                ],
                'visit' => [
                    'name' => $visit->name,
                    'primary_color' => $visit->primary_color,
                    'secondary_color' => $visit->secondary_color,
                    'rgb_primary' => $visit_rgb_primary,
                    'text_color' => $visit->text_color,
                    'shield' => (int)preg_replace('/\D+/', '', $visit->shieldFile),
                    'goals' => $data['visit']['goals'],
                    'formation' => $data['visit']['formation'],
                    'posession' => $data['visit']['posessionPer'],
                    'shots' => $data['visit']['shots'],
                    'shots_goal' => $data['visit']['shotsOnTarget'],
                    'yellow_cards' => (isset($data['visit']['yellow_cards']) ? $data['visit']['yellow_cards'] : -1),
                    'red_cards' => (isset($data['visit']['red_cards']) ? $data['visit']['red_cards'] : -1),
                    'substitutions' => (isset($data['visit']['substitutions']) ? $data['visit']['substitutions'] : -1),
                ],
                'scorers'               => $scorers,
                'highlights'            => $highlights
            ], 200);
        } else {
            $scorers = [[],[]];
            foreach ($data['scorers'] as $scorer) {
                if ($scorer[1] == 0) {
                    $scorers[0][] = $scorer[2] . ' <span>(' . substr($scorer[0], 0, 2) . '\')</span>';
                } else {
                    $scorers[1][] = '<span>(' . substr($scorer[0], 0, 2) . '\')</span> ' . $scorer[2];
                }
            }

            $actions = [];
            foreach ($data['plays'] as $play) {
                if (in_array($play[2], [1, 2, 5, 6, 7, 8, 9, 11, 12, 18, 19, 21, 22, 23, 24, 25, 26, 27, 28, 31, 32, 33, 34, 35, 36])) {
                    if (in_array($play[2], [6, 19, 23, 25, 26, 27])) {
                        $play[3] = '<strong>' . $play[3] . '</strong>';
                    }
                    $actions[] = [substr($play[0], 0, 2), 'rgba(' . implode(', ', ($play[1] == 0) ? $local_rgb_primary : $visit_rgb_primary) . ', 1);', (($play[1] == 0) ? $local['text_color'] : $visit['text_color']), $play[3]];
                }
            }

            $params = [
                'assistance' => empty($data['assistance']) ? 0 : $data['assistance'],
                'incomes' => empty($data['incomes']) ? 0 : $data['incomes'],
                'show_remaining' => $show_remaining,
                'remaining_time' => readableTime($remaining_time),
                'datetime' => date('d/m/Y H:i', $data['timestamp']),
                'stadium' => $data['stadium'],
                'local' => [
                    'name' => $local->name,
                    'primary_color' => $local->primary_color,
                    'secondary_color' => $local->secondary_color,
                    'rgb_primary' => $local_rgb_primary,
                    'text_color' => $local->text_color,
                    'shield_file' => $local->shieldFile,
                    'goals' => $data['local']['goals'],
                    'formation' => $data['local']['formation'],
                    'posession' => $data['local']['posessionPer'],
                    'shots' => $data['local']['shots'],
                    'shots_goal' => $data['local']['shotsOnTarget'],
                    'yellow_cards' => (isset($data['local']['yellow_cards']) ? $data['local']['yellow_cards'] : -1),
                    'red_cards' => (isset($data['local']['red_cards']) ? $data['local']['red_cards'] : -1),
                    'substitutions' => (isset($data['local']['substitutions']) ? $data['local']['substitutions'] : -1),
                ],
                'visit' => [
                    'name' => $visit->name,
                    'primary_color' => $visit->primary_color,
                    'secondary_color' => $visit->secondary_color,
                    'rgb_primary' => $visit_rgb_primary,
                    'text_color' => $visit->text_color,
                    'shield_file' => $visit->shieldFile,
                    'goals' => $data['visit']['goals'],
                    'formation' => $data['visit']['formation'],
                    'posession' => $data['visit']['posessionPer'],
                    'shots' => $data['visit']['shots'],
                    'shots_goal' => $data['visit']['shotsOnTarget'],
                    'yellow_cards' => (isset($data['visit']['yellow_cards']) ? $data['visit']['yellow_cards'] : -1),
                    'red_cards' => (isset($data['visit']['red_cards']) ? $data['visit']['red_cards'] : -1),
                    'substitutions' => (isset($data['visit']['substitutions']) ? $data['visit']['substitutions'] : -1),
                ],
                'scorers' => $scorers,
                'actions' => $actions,
            ];

            return view('match.result', $params);
        }
    }

    /**
     * Play match
     *
     * @param Request $request
     */
    public function play(Request $request)
    {
        $this->validate($request, [
            'rival'         => 'required|integer',
        ]);

        $api = FALSE;
        if ($request->expectsJson()) {
            if (empty(Auth::guard('api')->user())) {
                $user = Auth::user();
            } else {
                $user = Auth::guard('api')->user()->user;
                $api = TRUE;
            }
        } else {
            $user = Auth::user();
        }

        if ($request->input('rival') == $user->team->id) {
            return response()->json([
                'errors' => [
                    'type'      => 'match_play',
                    'message'   => 'Can\'t play against your own team'
                ]
            ], 500);
        }

        if ($request->input('rival') <= 26) {
            $match_type = 1;
        } else {
            $match_type = 2;
        }

        $remaining_time = 0;
        if ($match_type == 2) {
            $last_match = Matches::loadLastFriendlyMatch($user->team->id, $request->input('rival'));

            if (!empty($last_match)) {
                $last_match_time = strtotime($last_match->created_at);

                $remaining_time = 86400 - ($_SERVER['REQUEST_TIME'] - $last_match_time);
            }
        }

        if ($remaining_time > 0) {
            if ($api) {
                return response()->json([
                    'rival'                 => $request->input('rival'),
                    'log_file'              => $last_match->logfile,
                    'remaining'             => $remaining_time,
                    'remaining_readable'    => readableTime($remaining_time)
                ], 200);
            } else {
                return response()->json([
                    'file'      => $last_match->logfile,
                    'remaining' => readableTime($remaining_time)
                ], 200);
            }
        } else {
            $file_name = $_SERVER['REQUEST_TIME'] . '-' . $user->team->id . '-' . $request->input('rival') . '.log';
            $command = escapeshellcmd('python3 ' . base_path() . '/python/play.py ' . $user->team->id . ' ' . $request->input('rival') . ' ' . $match_type . ' -1 ' . $file_name);
            exec($command, $out, $status);
            if ($status == 0) {
                /**
                 * Copy log to S3
                 */
                Storage::disk('s3')->put(env('APP_ENV') . '/logs/' . $file_name, file_get_contents(base_path() . '/python/logs/' . $file_name));

                if ($api) {
                    return response()->json([
                        'rival'                 => $request->input('rival'),
                        'log_file'              => $file_name,
                        'remaining'             => 86400,
                        'remaining_readable'    => readableTime(86400)
                    ], 201);
                } else {
                    return response()->json([
                        'id'        => $request->input('rival'),
                        'file'      => $file_name,
                        'remaining' => readableTime(86400)
                    ], 201);
                }
            } else {
                if ($api) {
                    return response()->json([
                        'errors' => [
                            'type'      => 'match_play',
                            'message'   => 'An error occurred while playing the match'
                        ]
                    ], 500);
                } else {
                    return response()->json(['err_no' => $status], 500);
                }
            }
        }
    }

    /**
     * Show live match
     *
     * @param Matches $match
     * @return View
     */
    public function showLive(Matches $match)
    {
        if (Auth::check()) {
            if (!$match->exists) {
                $match = Auth::user()->team->live_match;

                if (!$match) {
                    return redirect()->route('home');
                }
            }
        } else {
            return redirect('/');
        }

        $other_matches = DB::table('matches_rounds')->join('matches', 'matches.id', 'matches_rounds.match_id')->where('round_id', $match->round->round_id)->where('matches.id', '!=', $match->id)->pluck('matches.logfile')->all();

        $aux = DB::table('tournament_positions')->select('tournament_positions.*', 'teams.name', 'teams.short_name')->join('teams', 'teams.id', 'tournament_positions.team_id')->join('tournament_rounds', 'tournament_rounds.category_id', 'tournament_positions.category_id')->where('tournament_rounds.id', $match->round->round_id)->get();
        $positions = [];
        foreach ($aux as $pos) {
            $positions[] = [
                'team_id'               => $pos->team_id,
                'team_name'             => $pos->name,
                'team_short_name'       => $pos->short_name,
                'base_position'         => $pos->last_position,
                'base_points'           => $pos->last_points,
                'base_goals_favor'      => $pos->last_goals_favor,
                'base_goals_against'    => $pos->last_goals_against,
                'position'              => $pos->last_position,
                'points'                => $pos->last_points,
                'goals_favor'           => $pos->last_goals_favor,
                'goals_against'         => $pos->last_goals_against,
                'goals_difference'      => $pos->last_goals_difference
            ];
        }

        $params = [
            'title'         => $match->local->name . ' - ' . $match->visit->name . ' en vivo',
            'match'         => $match,
            'match_others'  => json_encode($other_matches),
            'positions'     => json_encode($positions)
        ];

        return view('match.live', $params);
    }

    /**
     * Send matches logs for API
     *
     * @return Json
     */

    public function showLiveApi()
    {
        $team = Auth::guard('api')->user()->user->team;
        $match = $team->live_match;

        if (!$match) {
            return response()->json([
                'errors' => [
                    'type'      => 'live_match',
                    'message'   => 'Live broadcasting ended'
                ]
            ], 400);
        }

        $matches = DB::table('matches_rounds')->join('matches', 'matches.id', 'matches_rounds.match_id')->where('round_id', $match->round->round_id)->where('matches.id', '!=', $match->id)->pluck('matches.logfile')->all();
        array_unshift($matches, $match->logfile);

        $aux = DB::table('tournament_positions')->select('tournament_positions.*', 'teams.name', 'teams.short_name')->join('teams', 'teams.id', 'tournament_positions.team_id')->join('tournament_rounds', 'tournament_rounds.category_id', 'tournament_positions.category_id')->where('tournament_rounds.id', $match->round->round_id)->orderBy('last_position')->get();
        $positions = [];
        foreach ($aux as $pos) {
            $positions[] = [
                'team_id'               => $pos->team_id,
                'team_name'             => $pos->name,
                'team_short_name'       => $pos->short_name,
                'position'              => $pos->last_position,
                'points'                => $pos->last_points,
                'goals_favor'           => $pos->last_goals_favor,
                'goals_against'         => $pos->last_goals_against,
                'goals_difference'      => $pos->last_goals_difference
            ];
        }

        $output = [
            'category_name' => $match->category->name,
            'round_number'  => $match->round->number,
            'positions'     => $positions
        ];
        foreach ($matches as $logfile) {
            $data = getMatchLog($logfile);

            $version = isset($data['version']) ? $data['version'] : '0.0';

            $local = Team::find($data['local']['id']);
            $visit = Team::find($data['visit']['id']);

            $match = Matches::where('logfile', $logfile)->first();

            $local_rgb_primary = sscanf($local->primary_color, "#%02x%02x%02x");
            $visit_rgb_primary = sscanf($visit->primary_color, "#%02x%02x%02x");

            // Make translations
            if ($version != '0.0') {
                foreach ($data['plays'] as &$play) {
                    $play[3] = __('highlights.play_' . $play[2], $play[3]);
                }
            }

            $scorers = [
                'local' => [],
                'visit' => []
            ];
            foreach ($data['scorers'] as $scorer) {
                if ($scorer[1] == 0) {
                    $scorers['local'][] = [
                        'minutes'   => substr($scorer[0], 0, 2),
                        'player'    => $scorer[2]
                    ];
                } else {
                    $scorers['visit'][] = [
                        'minutes'   => substr($scorer[0], 0, 2),
                        'player'    => $scorer[2]
                    ];
                }
            }

            $plays = [];
            foreach ($data['plays'] as $play) {
                if (in_array($play[2], [1, 2, 4, 5, 6, 7, 8, 9, 11, 12, 14, 17, 18, 19, 21, 22, 23, 24, 25, 26, 27, 28, 31, 32, 33, 34])) {
                    $plays[] = [
                        'type'          => $play[2],
                        'team'          => $play[1],
                        'minutes'       => $play[0],
                        'background'    => ($play[1] == 0) ? $local->primary_color : $visit->primary_color,
                        'color'         => ($play[1] == 0) ? $local->text_color : $visit->text_color,
                        'highlight'     => $play[3]
                    ];
                }
            }

            $output['matches'][] = [
                'assistance'            => empty($data['assistance']) ? 0 : (int)$data['assistance'],
                'incomes'               => empty($data['incomes']) ? 0 : (int)$data['incomes'],
                'datetime'              => $match->created_at->timestamp,
                'stadium'               => $data['stadium'],
                'local' => [
                    'team_id'           => $local->id,
                    'name'              => $local->name,
                    'primary_color'     => $local->primary_color,
                    'secondary_color'   => $local->secondary_color,
                    'rgb_primary'       => $local_rgb_primary,
                    'text_color'        => $local->text_color,
                    'shield'            => (int)preg_replace('/\D+/', '', $local->shieldFile),
                    'goals'             => $data['local']['goals'],
                    'formation'         => $data['local']['formation'],
                    'posession'         => $data['local']['posessionPer'],
                    'shots'             => $data['local']['shots'],
                    'shots_goal'        => $data['local']['shotsOnTarget'],
                    'yellow_cards'      => (isset($data['local']['yellow_cards']) ? $data['local']['yellow_cards'] : -1),
                    'red_cards'         => (isset($data['local']['red_cards']) ? $data['local']['red_cards'] : -1),
                    'substitutions'     => (isset($data['local']['substitutions']) ? $data['local']['substitutions'] : -1),
                ],
                'visit' => [
                    'team_id'           => $visit->id,
                    'name'              => $visit->name,
                    'primary_color'     => $visit->primary_color,
                    'secondary_color'   => $visit->secondary_color,
                    'rgb_primary'       => $visit_rgb_primary,
                    'text_color'        => $visit->text_color,
                    'shield'            => (int)preg_replace('/\D+/', '', $visit->shieldFile),
                    'goals'             => $data['visit']['goals'],
                    'formation'         => $data['visit']['formation'],
                    'posession'         => $data['visit']['posessionPer'],
                    'shots'             => $data['visit']['shots'],
                    'shots_goal'        => $data['visit']['shotsOnTarget'],
                    'yellow_cards'      => (isset($data['visit']['yellow_cards']) ? $data['visit']['yellow_cards'] : -1),
                    'red_cards'         => (isset($data['visit']['red_cards']) ? $data['visit']['red_cards'] : -1),
                    'substitutions'     => (isset($data['visit']['substitutions']) ? $data['visit']['substitutions'] : -1),
                ],
                'scorers'               => $scorers,
                'plays'                 => $plays
            ];
        }

        return response()->json($output);
    }
}
