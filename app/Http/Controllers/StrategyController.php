<?php

namespace App\Http\Controllers;

use App\Strategy;
use Illuminate\Http\Request;

class StrategyController extends Controller
{
    /**
     * List all strategies for API
     */
    public function index()
    {
        $strategies = [];
        foreach (Strategy::get() as $strategy) {
            $strategies[] = [
                'id'        => $strategy->id,
                'name'      => $strategy->name,
                'positions' => $strategy->positionsToPercetages()
            ];
        }

        return response()->json([
            'strategies' => $strategies
        ], 200);
    }
}
