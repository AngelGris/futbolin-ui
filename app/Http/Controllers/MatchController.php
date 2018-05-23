<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Team;
use App\Matches;

class MatchController extends Controller
{
    /**
     * Retrieve log for live broadcast
     */
    public function getLiveLog($logfile)
    {
        $data = getMatchLog($logfile);

        $local = Team::find($data['local']['id']);
        $visit = Team::find($data['visit']['id']);

        $match = Matches::where('logfile', $logfile)->first();

        $data['time'] = (time() - $match->created_at->timestamp) * 5400 / (\Config::get('constants.LIVE_MATCH_DURATION') * 60);

        $data['logfile'] = $logfile;

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
     */
    public function load(Request $request)
    {
        $data = getMatchLog($request->file);
        $local = Team::find($data['local']['id']);
        $visit = Team::find($data['visit']['id']);

        $scorers = [[],[]];
        foreach ($data['scorers'] as $scorer) {
            if ($scorer[1] == 0) {
                $scorers[0][] = $scorer[2] . ' <span>(' . substr($scorer[0], 0, 2) . '\')</span>';
            } else {
                $scorers[1][] = '<span>(' . substr($scorer[0], 0, 2) . '\')</span> ' . $scorer[2];
            }
        }

        $local_rgb_primary = sscanf($local->primary_color, "#%02x%02x%02x");
        $visit_rgb_primary = sscanf($visit->primary_color, "#%02x%02x%02x");

        $actions = [];
        foreach ($data['plays'] as $play) {
            if (in_array($play[2], [1, 2, 6, 8, 9, 11, 12, 18, 19, 21, 22, 23, 24, 25, 26])) {
                if (in_array($play[2], [6, 19, 23, 25, 26])) {
                    $play[3] = '<strong>' . $play[3] . '</strong>';
                }
                $actions[] = [substr($play[0], 0, 2), 'rgba(' . implode(', ', ($play[1] == 0) ? $local_rgb_primary : $visit_rgb_primary) . ', 1);', (($play[1] == 0) ? $local['text_color'] : $visit['text_color']), $play[3]];
            }
        }

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

    /**
     * Play match
     */
    public function play(Request $request)
    {
        $remaining_time = 0;
        if ($request->match_type == 2) {
            $last_match = Matches::loadLastFriendlyMatch(Auth::user()->team->id, $request->rival);

            if (!empty($last_match)) {
                $last_match_time = strtotime($last_match->created_at);

                $remaining_time = 86400 - ($_SERVER['REQUEST_TIME'] - $last_match_time);
            }
        }

        if ($remaining_time > 0) {
            return json_encode(['file' => $last_match->logfile, 'remaining' => readableTime($remaining_time)]);
        } else {
            $file_name = $_SERVER['REQUEST_TIME'] . '-' . Auth::user()->team->id . '-' . $request->rival . '.log';
            $command = escapeshellcmd('python3 ' . base_path() . '/python/play.py ' . Auth::user()->team->id . ' ' . $request->rival . ' ' . $request->match_type . ' -1 ' . $file_name);
            exec($command, $out, $status);
            if ($status == 0) {
                return json_encode(['id' => $request->rival, 'file' => $file_name, 'remaining' => readableTime(86400)]);
            } else {
                return json_encode(['err_no' => $status]);
            }
        }
    }

    /**
     * Show live match
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

        $params = [
            'title' => $match->local->name . ' - ' . $match->visit->name . ' en vivo',
            'match' => $match,
            'match_others' => json_encode($other_matches)
        ];

        return view('match.live', $params);
    }
}
