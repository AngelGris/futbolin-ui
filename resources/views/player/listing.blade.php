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
            <td align="right"><strong>{{ $player['average'] }}</strong></td>
            <td align="right">{{ $player['goalkeeping'] }}</td>
            <td align="right">{{ $player['defending'] }}</td>
            <td align="right">{{ $player['dribbling'] }}</td>
            <td align="right">{{ $player['heading'] }}</td>
            <td align="right">{{ $player['jumping'] }}</td>
            <td align="right">{{ $player['passing'] }}</td>
            <td align="right">{{ $player['precision'] }}</td>
            <td align="right">{{ $player['speed'] }}</td>
            <td align="right">{{ $player['strength'] }}</td>
            <td align="right">{{ $player['tackling'] }}</td>
            <td align="right">{{ $player['experience'] }}</td>
        @endforeach
    </tbody>
</table>
<span class="fa fa-user-times" style="color:#f00;"></span> = jugadores que se retiran al final de la temporada
@endsection