@extends('layouts.inner')

@section('content-inner')
<h3>{{ $player['number'] }} - {!! $player['name'] !!}</h3>
<h4>{{ $player['position'] }} - {{ $player['age'] }} años</h4>
<div class="col-xs-12 col-sm-6 col-md-3 zebra">
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
@endsection
