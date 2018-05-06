<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $vars = [
            'payments' => Payment::orderBy('created_at', 'DESC')->paginate(20),
        ];

        return view('admin.payment.index', $vars);
    }
}
