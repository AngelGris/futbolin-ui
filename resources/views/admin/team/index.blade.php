@extends('layouts.admin')

@section('content-inner')
<div class="col-xs-12 zebra">
    <h3>Equipos</h3>
    @foreach($teams as $team)
    <div class="col-xs-12">
        <div class="col-xs-3">{{ $team['name'] }}</div>
        <div class="col-xs-3">{{ $team['user']['name'] }}</div>
        <div class="col-xs-2">{{ $team['trainning_count'] }}</div>
        <div class="col-xs-3"{!! !$team['inTrainningSpam'] ? ' style="color:#f00;"' : ($team['trainable'] ? ' style="color:#0b0;"' : '') !!}>{{ date('d/m/Y H:i:s', strtotime($team['last_trainning'])) }}</div>
        <div class="col-xs-1"><a href="{{ route('admin.team', ['domain' => getDomain(), 'id' => $team['id']]) }}"><span class="fa fa-search"></span></a></div>
    </div>
    @endforeach
</div>
@endsection
