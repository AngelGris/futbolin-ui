@extends('layouts.inner')

@section('javascript-inner')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
$(function(){
    $('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false
    });

    $('#players-filtering').change(function() {
        if ($(this).val() != '') {
            $('tbody tr').hide();
            $('tbody tr.' + $(this).val()).show();
        } else {
            $('tbody tr').show();
        }
    });
});
</script>
@endsection

@section('content-inner')
Mostrar:
<select id="players-filtering">
    <option value="">Todos</option>
    <option value="arq">Arqueros</option>
    <option value="def">Defensores</option>
    <option value="med">Mediocampistas</option>
    <option value="ata">Atacantes</option>
</select>
<table id="dyntable" class="table table-bordered table-players responsive">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Posición">POS</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Energía">ENE</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Media">MED</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Arquero">ARQ</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Defensa">DEF</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Gambeta">GAM</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Cabeceo">CAB</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Salto">SAL</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Pase">PAS</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Precisión">PRE</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Velocidad">VEL</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Fuerza">FUE</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Quite">QUI</span></th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Experiencia">EXP</span></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($players as $player)
        <tr class="{{ strtolower($player['position']) }}">
            <td align="right">{{ $player['number'] }}</td>
            <td><a href="{{ route('player', $player['id']) }}">{!! $player['name']  !!}</a></td>
            <td align="center">{{ $player['age'] }}</td>
            <td align="center"><span data-placement="top" data-toggle="tooltip" data-original-title="{{ $player['position_long'] }}">{{ $player['position'] }}</span></td>
            <td align="right">{{ $player['stamina'] }}</td>
            <td align="right"><strong>{{ $player['average'] }}</strong></td>
            @foreach (['goalkeeping', 'defending', 'dribbling', 'heading', 'jumping', 'passing', 'precision', 'speed', 'strength', 'tackling'] as $attr)
            <td align="right">
                @if ($player['upgraded'] && !empty($player['last_upgrade'][$attr]))
                <span style="color:#009900;">{{ $player[$attr] }}<sub>+{{ $player['last_upgrade'][$attr] }}</sub></span>
                @else
                {{ $player[$attr] }}
                @endif
            </td>
            @endforeach
            <td align="right">{{ $player['experience'] }}</td>
        @endforeach
    </tbody>
</table>
<span class="fa fa-user-times" style="color:#f00;"></span> = jugadores que se retiran al final de la temporada<br>
<span class="fa fa-square" style="color:#ff0;"></span> = jugadores con 4 tarjetas amarillas<br>
<span class="fa fa-square" style="color:#f00;"></span> = jugadores suspendido<br>
<span class="fa fa-medkit" style="color:#f00;"></span> = jugadores lesionados<br>
<span class="fa fa-arrow-circle-up" style="color:#080;"></span> = jugadores mejorados después del último partido<br>
<span class="fa fa-arrow-down" style="color:#f00;"></span> = jugadores con poca energía<br>
@endsection