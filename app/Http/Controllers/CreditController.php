<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CreditItem;

class CreditController extends Controller
{
    /**
     * Credits index page
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => CreditItem::get()
            ], 200);
        } else {
            $vars = [
                'icon'      => 'fa fa-soccer-ball-o',
                'title'     => 'Fúlbos',
                'subtitle'  => 'Un empujoncito no viene mal',
                'items'     => CreditItem::get()
            ];

            return view('credits.index', $vars);
        }
    }
}
