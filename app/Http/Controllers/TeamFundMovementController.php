<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamFundMovementController extends Controller
{
    /**
     * Show team's movements
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $team = Auth::guard('api')->user()->user->team;

            return response()->json($team->fundMovements()->paginate(50), 200);
        } else {
            $team = Auth::user()->team;

            $vars = [
                'icon' => 'fa fa-money',
                'title' => __('headers.finances_title'),
                'subtitle' => __('headers.finances_subtitle'),
                'movements' => $team->fundMovements()->paginate(50)
            ];

            return view('team.finances', $vars);
        }
    }
}
