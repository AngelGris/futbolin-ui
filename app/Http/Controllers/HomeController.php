<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if (is_null(Auth::user()->team)) {
            return redirect('/equipo/crear');
        }

        $vars = [
            'icon' => 'iconfa-home',
            'title' => 'Vestuario',
            'subtitle' => 'Aquí comienza todo'
        ];

        return view('home', $vars);
    }
}
