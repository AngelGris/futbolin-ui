<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AdminMessage;

class MessageController extends Controller
{
    public function index()
    {
        $vars = [
            'messages' => AdminMessage::latest()->get(),
        ];

        return view('admin.message.index', $vars);
    }

    public function show($domain, AdminMessage $message)
    {
        return json_encode([
            'title' => $message->title,
            'message' => $message->message
        ]);
    }

    public function showPublic(AdminMessage $message)
    {
        return json_encode([
            'title' => $message->title,
            'message' => $message->message
        ]);
    }

    public function create()
    {
        $vars = [
            'editing' => FALSE,
            'message' => [
                'from_date' => date('d/m/Y'),
                'from_time' => date('H:i'),
                'to_date' => date('d/m/Y', $_SERVER['REQUEST_TIME'] + 607800),
                'to_time' => date('H:i'),
                'title' => '',
                'message' => '',
            ],
        ];

        return view('admin.message.create', $vars);
    }

    public function store(Request $request)
    {
        $valid_date = explode('/', $request['from_date']);
        $valid_time = explode(':', $request['from_time']);
        $request['valid_from'] = date('Y-m-d H:i:s', mktime($valid_time[0], $valid_time[1], 0, $valid_date[1], $valid_date[0], $valid_date[2]));
        $valid_date = explode('/', $request['to_date']);
        $valid_time = explode(':', $request['to_time']);
        $request['valid_to'] = date('Y-m-d H:i:s', mktime($valid_time[0], $valid_time[1], 0, $valid_date[1], $valid_date[0], $valid_date[2]));
        $this->validate($request, [
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'title' => 'required|max:255',
            'message' => 'required',
        ]);

        AdminMessage::create($request->only(['valid_from', 'valid_to', 'title', 'message']));

        return redirect(route('admin.messages', getDomain()));
    }

    public function edit($domain, AdminMessage $message)
    {
        $message['from_date'] = date('d/m/Y', strtotime($message['valid_from']));
        $message['from_time'] = date('H:i', strtotime($message['valid_from']));
        $message['to_date'] = date('d/m/Y', strtotime($message['valid_to']));
        $message['to_time'] = date('H:i', strtotime($message['valid_to']));
        $vars = [
            'editing' => TRUE,
            'message' => $message
        ];

        return view('admin.message.create', $vars);
    }

    public function save(Request $request, $domain, AdminMessage $message)
    {
        $valid_date = explode('/', $request['from_date']);
        $valid_time = explode(':', $request['from_time']);
        $request['valid_from'] = date('Y-m-d H:i:s', mktime($valid_time[0], $valid_time[1], 0, $valid_date[1], $valid_date[0], $valid_date[2]));
        $valid_date = explode('/', $request['to_date']);
        $valid_time = explode(':', $request['to_time']);
        $request['valid_to'] = date('Y-m-d H:i:s', mktime($valid_time[0], $valid_time[1], 0, $valid_date[1], $valid_date[0], $valid_date[2]));
        $this->validate($request, [
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'title' => 'required|max:255',
            'message' => 'required',
        ]);

        $message->fill($request->only(['valid_from', 'valid_to', 'title', 'message']));
        $message->save();

        return redirect(route('admin.messages', getDomain()));
    }

    public function delete($domain, AdminMessage $message) {
        $message->delete();

        return redirect(route('admin.messages', getDomain()));
    }
}
