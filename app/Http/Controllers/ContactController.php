<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class ContactController extends Controller
{
    public function index()
    {
        $params['title'] = 'Contacto';
        $params['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $params['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        return view('contact.index', $params);
    }

    public function send(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|min:10'
        ]);

        $msg = 'Nombre: ' . $request->input('name') . "\n";
        $msg .= 'E-mail: ' . $request->input('email') . "\n";
        $msg .= 'Mensaje:' . "\n";
        $msg .= $request->input('message');

        Mail::raw($msg, function($message)
        {
            $message->from('contacto@futbolin.com.ar', 'Futbolin');
            $message->to('lucianogarciabes@gmail.com')->subject('Contacto desde Futbolin');
        });

        return redirect()->route('contact.thanks');
    }

    public function thanks()
    {
        $params['title'] = 'Gracias por contactarnos';
        $params['bodyclass'] = 'class="loginpage"';

        $num = rand(1, 9);
        $params['bodystyle'] = 'style="background-image:url(/img/back/' . sprintf("%03d", $num) . '.jpg);"';

        return view('contact.thanks', $params);
    }
}
