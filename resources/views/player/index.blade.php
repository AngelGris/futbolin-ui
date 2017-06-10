@extends('layouts.inner')

@section('content-inner')
<h3>{{ $player['number'] }} - {!! $player['name'] !!}</h3>
<h4>{{ $player['position'] }} - {{ $player['age'] }} años</h4>
<div class="col-xs-12 col-sm-6 col-md-3 zebra">
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Arquero</label>
        <div class="col-md-2">{{ $player['goalkeeping'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Defensa</label>
        <div class="col-md-2">{{ $player['defending'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Gambeta</label>
        <div class="col-md-2">{{ $player['dribbling'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Cabeceo</label>
        <div class="col-md-2">{{ $player['heading'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Salto</label>
        <div class="col-md-2">{{ $player['jumping'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Pase</label>
        <div class="col-md-2">{{ $player['passing'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Precisión</label>
        <div class="col-md-2">{{ $player['precision'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Velocidad</label>
        <div class="col-md-2">{{ $player['speed'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Fuerza</label>
        <div class="col-md-2">{{ $player['strength'] }}</div>
    </div>
    <div class="col-xs-12">
        <label class="col-md-10 control-label">Quite</label>
        <div class="col-md-2">{{ $player['tackling'] }}</div>
    </div>
</div>
@endsection
