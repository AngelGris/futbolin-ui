@extends('layouts.inner')

@section('content-inner')
@if ($player->treatable)
<h3>{{ $player['number'] }} - {!! $player['name'] !!} <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-treatment">Tratar jugador</button></h3>
@else
<h3>{{ $player['number'] }} - {!! $player['name'] !!}</h3>
@endif
<h4>{{ $player['position'] }} - {{ $player['age'] }} años</h4>
<div class="col-xs-12 col-sm-6 col-md-3 zebra">
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">Energía</label>
        <div class="col-xs-3">{{ $player['stamina'] }}</div>
    </div>
    @foreach (['Arquero' => 'goalkeeping', 'Defensa' => 'defending', 'Gambeta' => 'dribbling', 'Cabeceo' => 'heading', 'Salto' => 'jumping', 'Pase' => 'passing', 'Precisión' => 'precision', 'Velocidad' => 'speed', 'Fuerza' => 'strength', 'Quite' => 'tackling'] as $k => $v)
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">{{ $k }}</label>
        <div class="col-xs-3">
            @if ($player['upgraded'] && !empty($player['last_upgrade'][$v]))
            <span style="color:#009900;">{{ $player[$v] }}<sub>+{{ $player['last_upgrade'][$v] }}</sub></span>
            @else
            {{ $player[$v] }}
            @endif
        </div>
    </div>
    @endforeach
    <div class="col-xs-12">
        <label class="col-xs-9 control-label">Experiencia</label>
        <div class="col-xs-3">{{ $player['experience'] }}</div>
    </div>
</div>
@if ($player->treatable)
<div class="modal fade" id="modal-treatment" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Tratar jugador</h4>
            </div>
            @if($_user->credits > 0)
            <div class="modal-body">
                <p>Si tratas a <b>{{ $player->first_name }} {{ $player->last_name }}</b> ahora recuperará <b>{{ $player->treatmentImprovement }} {{ str_plural('fecha', $player->treatmentImprovement) }}</b></p>
                <p>Cada jugador puede ser tratado una sóla vez por cada lesión.</p>
                <p>¿Quieres tratar a {{ $player->short_name }} por 1 Fúlbo?</p>
            </div>
            <div class="modal-footer">
                <form method="post" action="{{ route('shopping.buy') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="4">
                    <input type="hidden" name="player_id" value="{{ $player->id }}">
                    <button id="buy-item" class="btn btn-primary">Tratar</button>
                    <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
                </form>
            </div>
            @else
            <div class="modal-body">
                <p>No tienes suficientes Fúlbos para tratar a {{ $player->shot_name }}.</p>
                <p>¿Quieres comprar más Fúlbos?</p>
            </div>
            <div class="modal-footer">
                <a href="{{ route('shopping.credits') }}" class="btn btn-primary">Comprar Fúlbos</a>
                <button type="button" data-dismiss="modal" class="btn">Cancelar</button>
            </div>
            @endif
        </div>
    </div>
</div>
@endif
@endsection
