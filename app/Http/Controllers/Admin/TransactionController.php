<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transaction;

class TransactionController extends Controller
{
    public function index()
    {
        $vars = [
            'transactions' => Transaction::orderBy('created_at', 'DESC')->paginate(20),
        ];

        return view('admin.transaction.index', $vars);
    }
}
