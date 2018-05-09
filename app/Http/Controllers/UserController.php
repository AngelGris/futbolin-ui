<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Send User information through API
     */
    public function index()
    {
        $user = Auth::guard('api')->user()->user;
        return response()->json([
            'user' => $user
        ], 200);
    }
}
