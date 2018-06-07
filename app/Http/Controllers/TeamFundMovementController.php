<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamFundMovementController extends Controller
{
    /**
     * Show team's movements
     */
    public function index()
    {
        $team = Auth::user()->team;

        $vars = [
            'icon' => 'fa fa-money',
            'title' => 'Finanzas',
            'subtitle' => 'Money, money, money',
            'movements' => $team->fundMovements()->paginate(50)
        ];

        return view('team.finances', $vars);
    }
}
