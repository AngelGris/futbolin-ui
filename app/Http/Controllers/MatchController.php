<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Team;
use App\Matches;

class MatchController extends Controller
{
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
            if (in_array($play[2], [1, 2, 6, 8, 9, 11, 12, 18, 19, 21, 22, 23])) {
                if (in_array($play[2], [6, 19])) {
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
                'substitutions' => (isset($data['visit']['substitutions']) ? $data['visit']['substitutions'] : -1),
            ],
            'scorers' => $scorers,
            'actions' => $actions,
        ];

        return view('match.result', $params);
    }
}
