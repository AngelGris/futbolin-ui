@extends('layouts.admin')

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Usuarios</h3>
    @foreach($users as $user)
    <div class="col-xs-12">
        <div class="col-xs-5">{{ $user['name'] }}</div>
        <div class="col-xs-5">{{ $user['team']['name'] }}</div>
        <div class="col-xs-2"><a href="{{ route('admin.user', ['domain' => getDomain(), 'id' => $user['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
</div>
@endsection
