<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Team;

class MatchController extends Controller
{
    /**
     * Play match
     */
    public function play(Request $request)
    {
        $file_name = $_SERVER['REQUEST_TIME'] . '-' . Auth::user()->team->id . '-' . $request->rival . '.log';
        $command = escapeshellcmd('python3 ' . base_path() . '/python/play.py ' . Auth::user()->team->id . ' ' . $request->rival . ' 0 ' . $file_name);
        exec($command, $out, $status);
        if ($status == 0) {
            return json_encode(['file' => $file_name]);
        } else {
            return FALSE;
        }
    }

    /**
     * Load match result
     */
    public function load(Request $request)
    {
        $file_name = base_path() . '/python/logs/' . $request->file;

        $string = file_get_contents($file_name);
        $data = json_decode($string,true);

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
            if (in_array($play[2], [1, 2, 6, 8, 9, 12, 18, 19])) {
                if (in_array($play[2], [6, 19])) {
                    $play[3] = '<strong>' . $play[3] . '</strong>';
                }
                $actions[] = [substr($play[0], 0, 2), 'rgba(' . implode(', ', ($play[1] == 0) ? $local_rgb_primary : $visit_rgb_primary) . ', 1);', (($play[1] == 0) ? $local['text_color'] : $visit['text_color']), $play[3]];
            }
        }

        $params = [
            'local' => [
                'name' => $local->name,
                'primary_color' => $local->primary_color,
                'secondary_color' => $local->secondary_color,
                'rgb_primary' => $local_rgb_primary,
                'text_color' => $local->text_color,
                'goals' => $data['local']['goals'],
                'formation' => $data['local']['formation'],
                'posession' => $data['local']['posessionPer'],
                'shots' => $data['local']['shots'],
                'shots_goal' => $data['local']['shotsOnTarget'],
            ],
            'visit' => [
                'name' => $visit->name,
                'primary_color' => $visit->primary_color,
                'secondary_color' => $visit->secondary_color,
                'rgb_primary' => $visit_rgb_primary,
                'text_color' => $visit->text_color,
                'goals' => $data['visit']['goals'],
                'formation' => $data['visit']['formation'],
                'posession' => $data['visit']['posessionPer'],
                'shots' => $data['visit']['shots'],
                'shots_goal' => $data['visit']['shotsOnTarget'],
            ],
            'scorers' => $scorers,
            'actions' => $actions,
        ];

        return view('match.result', $params);
    }
}
