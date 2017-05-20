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
        return getMatchLog($match->logfile);
    }
}
