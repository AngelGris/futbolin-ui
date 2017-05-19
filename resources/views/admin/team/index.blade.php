@extends('layouts.admin')

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Equipos</h3>
    @foreach($teams as $team)
    <div class="col-xs-12">
        <div class="col-xs-5">{{ $team['name'] }}</div>
        <div class="col-xs-5">{{ $team['user']['name'] }}</div>
        <div class="col-xs-2"><a href="{{ route('admin.team', ['domain' => getDomain(), 'id' => $team['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
</div>
@endsection
