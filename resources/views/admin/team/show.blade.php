@extends('layouts.admin')

@section('content-inner')
<div class="col-md-6 zebra">
    <h3>{{ $team['name'] }}</h3>
    <div class="col-xs-12">
        <div class="col-xs-6">Entrenador</div>
        <div class="col-xs-6"><a href="{{ route('admin.user', [getDomain(), $team['user']['id']]) }}">{{ $team['user']['name'] }}</a></div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Creado</div>
        <div class="col-xs-6">{{ $team['created_at'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Actualizado</div>
        <div class="col-xs-6">{{ $team['updated_at'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Nombre Corto</div>
        <div class="col-xs-6">{{ $team['short_name'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Color Primario</div>
        <div class="col-xs-6"><div class="col-xs-12" style="background-color:{{ $team['primary_color'] }};">&nbsp;</div></div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Color Secundario</div>
        <div class="col-xs-6"><div class="col-xs-12" style="background-color:{{ $team['secondary_color'] }};">&nbsp;</div></div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Color del Texto</div>
        <div class="col-xs-6"><div class="col-xs-12" style="background-color:{{ $team['text_color'] }};">&nbsp;</div></div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Estadio</div>
        <div class="col-xs-6">{{ $team['stadium_name'] }}</div>
    </div>
    <div class="col-xs-12">
        <div class="col-xs-6">Estrategia</div>
        <div class="col-xs-6">{{ $team['strategy']['name'] }}</div>
    </div>
</div>
@endsection
