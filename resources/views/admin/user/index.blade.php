@extends('layouts.admin')

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Usuarios</h3>
    @foreach($users as $user)
    <div class="col-xs-12">
        <div class="col-xs-4">{{ $user['name'] }}</div>
        <div class="col-xs-3">{{ $user['team']['name'] }}</div>
        <div class="col-xs-3">{{ date('d/m/Y H:i:s', strtotime($user['last_activity'])) }}</div>
        <div class="col-xs-2"><a href="{{ route('admin.user', ['domain' => getDomain(), 'id' => $user['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
</div>
@endsection
