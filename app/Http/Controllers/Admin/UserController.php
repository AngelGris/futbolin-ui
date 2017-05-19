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
            'users' => User::where('id', '>', 1)->orderBy('first_name')->orderBy('last_name')->get()
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
