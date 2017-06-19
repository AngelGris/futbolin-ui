<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        $vars = [
            'users' => User::where('id', '>', 1)->orderBy('last_activity', 'DESC')->get(),
        ];

        return view('admin.user.index', $vars);
    }

    public function show($domain, \App\User $user)
    {
        $vars = [
            'user' => $user,
        ];

        return view('admin.user.show', $vars);
    }
}
