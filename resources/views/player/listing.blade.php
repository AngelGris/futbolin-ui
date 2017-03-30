@extends('layouts.inner')

@section('javascript-inner')
<script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('#dyntable').dataTable({
        "paging": false,
        "searching": false,
        "info": false
    });
});
</script>
@endsection

@section('content-inner')
<table id="dyntable" class="table table-bordered responsive">
    <thead>
        <tr>
            <th>#</th>
            <th style="width:50%">Nombre</th>
            <th><span data-placement="top" data-toggle="tooltip" data-original-title="Posición">POS</span></th>
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
        </tr>
    </thead>
    <tbody>
        @foreach ($players as $player)
        <tr class="{{ strtolower($player['position']) }}">
            <td align="right">{{ $player['number'] }}</td>
            <td>{{ $player['first_name'] . ' ' . $player['last_name'] }}</td>
            <td align="center">{{ $player['position'] }}</td>
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
        @endforeach
    </tbody>
</table>
@endsection