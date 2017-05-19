<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Matches;

class MatchController extends Controller
{
    public function index()
    {
        $vars = [
            'matches' => Matches::latest()->get(),
        ];

        return view('admin.match.index', $vars);
    }

    public function showLog($domain, \App\Matches $match)
    {
        $file_name = base_path() . '/python/logs/' . $match->logfile;

        $string = file_get_contents($file_name);

        return json_decode($string,true);
    }
}
